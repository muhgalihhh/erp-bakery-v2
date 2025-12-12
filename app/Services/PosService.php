<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerPointsLedger;
use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * POS Service - Comprehensive Point of Sale Logic
 *
 * Handles:
 * - Cart management with stock validation
 * - Sales order creation with full ERP integration
 * - Stock movement (OUT) for each item sold
 * - Journal entries for accounting
 * - Customer points (earn & redeem)
 * - Payment processing
 */
class PosService
{
    protected JournalService $journalService;
    protected DiscountEngine $discountEngine;

    public function __construct(JournalService $journalService, DiscountEngine $discountEngine)
    {
        $this->journalService = $journalService;
        $this->discountEngine = $discountEngine;
    }

    /**
     * Get all active discount rules for today
     * Including day-based discounts, birthday discounts, etc.
     */
    public function getActiveDiscountsToday(): Collection
    {
        $today = now();
        $dayOfWeek = $today->dayOfWeek; // 0 = Sunday, 6 = Saturday

        return DiscountRule::query()
            ->where('is_active', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where(function ($query) {
                $query->where('usage_limit', 0)
                    ->orWhereNull('usage_limit')
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            })
            ->orderBy('priority', 'desc')
            ->get()
            ->map(function ($rule) use ($dayOfWeek) {
                $conditions = $rule->conditions ?? [];
                $actions = $rule->actions ?? [];

                // Determine discount type for display
                $discountType = 'general';
                $discountLabel = '';

                // Check if it's a birthday discount
                if (!empty($conditions['is_birthday'])) {
                    $discountType = 'birthday';
                    $discountLabel = '🎂 Diskon Ulang Tahun';
                }
                // Check if it's day-specific
                elseif (!empty($conditions['day_of_week'])) {
                    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $applicableDays = array_map(fn($d) => $dayNames[$d] ?? '', $conditions['day_of_week']);
                    $discountLabel = '📅 ' . implode(', ', $applicableDays);
                    $discountType = 'day_specific';
                }
                // Check if first purchase
                elseif (!empty($conditions['is_first_purchase'])) {
                    $discountType = 'first_purchase';
                    $discountLabel = '🌟 Pembelian Pertama';
                }
                // Check if tier specific
                elseif (!empty($conditions['customer_tier_codes'])) {
                    $discountType = 'tier_specific';
                    $discountLabel = '👑 Member Eksklusif';
                }
                // Check if product specific
                elseif (!empty($conditions['required_product_ids'])) {
                    $discountType = 'product_specific';
                    $discountLabel = '🏷️ Produk Tertentu';
                }

                // Calculate discount value for display
                $discountValueDisplay = '';
                if (isset($actions['discount_type'])) {
                    if ($actions['discount_type'] === 'percentage') {
                        $discountValueDisplay = ($actions['discount_value'] ?? 0) . '%';
                    } elseif ($actions['discount_type'] === 'fixed') {
                        $discountValueDisplay = 'Rp ' . number_format($actions['discount_value'] ?? 0, 0, ',', '.');
                    } elseif ($actions['discount_type'] === 'free_item') {
                        $discountValueDisplay = 'Free Item';
                    }
                }

                // Check if applicable today (for day-specific)
                $isApplicableToday = true;
                if (!empty($conditions['day_of_week'])) {
                    $isApplicableToday = in_array($dayOfWeek, $conditions['day_of_week']);
                }

                return [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'code' => $rule->code,
                    'description' => $rule->description,
                    'discount_type' => $discountType,
                    'discount_label' => $discountLabel,
                    'discount_value' => $discountValueDisplay,
                    'conditions' => $conditions,
                    'actions' => $actions,
                    'min_subtotal' => $conditions['min_subtotal'] ?? null,
                    'required_product_ids' => $conditions['required_product_ids'] ?? [],
                    'is_applicable_today' => $isApplicableToday,
                    'is_birthday' => !empty($conditions['is_birthday']),
                    'is_first_purchase' => !empty($conditions['is_first_purchase']),
                    'end_date' => $rule->end_date?->format('d M Y'),
                    'priority' => $rule->priority,
                ];
            });
    }

    /**
     * Get products that have active discounts
     * Returns product IDs mapped to their discount info
     */
    public function getProductsWithDiscounts(): array
    {
        $activeDiscounts = $this->getActiveDiscountsToday();
        $productDiscounts = [];

        foreach ($activeDiscounts as $discount) {
            if (!empty($discount['required_product_ids'])) {
                foreach ($discount['required_product_ids'] as $productId) {
                    if (!isset($productDiscounts[$productId])) {
                        $productDiscounts[$productId] = [];
                    }
                    $productDiscounts[$productId][] = [
                        'name' => $discount['name'],
                        'value' => $discount['discount_value'],
                        'type' => $discount['discount_type'],
                    ];
                }
            }
        }

        return $productDiscounts;
    }

    /**
     * Check if a customer has birthday today
     */
    public function isCustomerBirthday(?int $customerId): bool
    {
        if (!$customerId) {
            return false;
        }

        $customer = Customer::find($customerId);
        if (!$customer || !$customer->date_of_birth) {
            return false;
        }

        $today = now();
        $birthday = Carbon::parse($customer->date_of_birth);

        return $today->month === $birthday->month && $today->day === $birthday->day;
    }

    /**
     * Get customers who have birthday today
     */
    public function getBirthdayCustomersToday(): Collection
    {
        $today = now();

        return Customer::query()
            ->where('is_active', true)
            ->whereNotNull('date_of_birth')
            ->whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day)
            ->with('tier')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'tier_name' => $customer->tier?->name ?? 'Regular',
                    'tier_color' => $customer->tier?->color ?? 'gray',
                    'total_points' => (float) $customer->total_points,
                ];
            });
    }

    /**
     * Get discount info for a specific customer
     * Returns applicable discounts including birthday if applicable
     */
    public function getCustomerDiscountInfo(?int $customerId): array
    {
        $discounts = [];

        if (!$customerId) {
            return $discounts;
        }

        $customer = Customer::with('tier')->find($customerId);
        if (!$customer) {
            return $discounts;
        }

        // Check birthday
        if ($this->isCustomerBirthday($customerId)) {
            // Find birthday discount rule
            $birthdayDiscount = DiscountRule::where('is_active', true)
                ->whereJsonContains('conditions->is_birthday', true)
                ->first();

            if ($birthdayDiscount) {
                $actions = $birthdayDiscount->actions ?? [];
                $discounts[] = [
                    'type' => 'birthday',
                    'name' => $birthdayDiscount->name,
                    'label' => '🎂 Selamat Ulang Tahun!',
                    'value' => $this->formatDiscountValue($actions),
                ];
            } else {
                // Even without specific rule, mark birthday
                $discounts[] = [
                    'type' => 'birthday',
                    'name' => 'Birthday',
                    'label' => '🎂 Hari ini Ulang Tahun!',
                    'value' => '',
                ];
            }
        }

        // Check tier discount
        if ($customer->tier && $customer->tier->discount_percentage > 0) {
            $discounts[] = [
                'type' => 'tier',
                'name' => $customer->tier->name,
                'label' => '👑 ' . $customer->tier->name,
                'value' => $customer->tier->discount_percentage . '%',
            ];
        }

        // Check first purchase
        $hasOrders = SalesOrder::where('customer_id', $customerId)
            ->where('status', SalesOrder::STATUS_COMPLETED)
            ->exists();

        if (!$hasOrders) {
            $firstPurchaseDiscount = DiscountRule::where('is_active', true)
                ->whereJsonContains('conditions->is_first_purchase', true)
                ->first();

            if ($firstPurchaseDiscount) {
                $actions = $firstPurchaseDiscount->actions ?? [];
                $discounts[] = [
                    'type' => 'first_purchase',
                    'name' => $firstPurchaseDiscount->name,
                    'label' => '🌟 Pembelian Pertama',
                    'value' => $this->formatDiscountValue($actions),
                ];
            }
        }

        return $discounts;
    }

    /**
     * Format discount value for display
     */
    protected function formatDiscountValue(array $actions): string
    {
        if (!isset($actions['discount_type'])) {
            return '';
        }

        switch ($actions['discount_type']) {
            case 'percentage':
                return ($actions['discount_value'] ?? 0) . '%';
            case 'fixed':
                return 'Rp ' . number_format($actions['discount_value'] ?? 0, 0, ',', '.');
            case 'free_item':
                return 'Free Item';
            default:
                return '';
        }
    }

    /**
     * Generate unique order number
     */
    public function generateOrderNumber(): string
    {
        $prefix = 'SO';
        $yearMonth = Carbon::now()->format('Ym');

        $latest = SalesOrder::withTrashed()
            ->where('order_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderBy('order_number', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->order_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $yearMonth, $newNumber);
    }

    /**
     * Validate stock availability for all cart items
     *
     * @param array $cartItems
     * @return array ['valid' => bool, 'errors' => array, 'warnings' => array]
     */
    public function validateStock(array $cartItems): array
    {
        $errors = [];
        $warnings = [];

        foreach ($cartItems as $index => $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                $errors[] = "Produk {$item['name']} tidak ditemukan.";
                continue;
            }

            if (!$product->is_active) {
                $errors[] = "Produk {$product->name} tidak aktif.";
                continue;
            }

            if (!$product->is_sellable) {
                $errors[] = "Produk {$product->name} tidak dapat dijual.";
                continue;
            }

            // Check stock (in stock UOM)
            $requiredQty = $item['quantity'];
            $availableStock = (float) $product->current_stock;

            if ($requiredQty > $availableStock) {
                $errors[] = "Stok {$product->name} tidak cukup. Tersedia: " .
                    number_format($availableStock, 2) . " {$product->uom_stock}, " .
                    "dibutuhkan: {$requiredQty} {$product->uom_stock}";
            } elseif (($availableStock - $requiredQty) <= $product->minimum_stock) {
                $warnings[] = "Stok {$product->name} akan mencapai batas minimum setelah transaksi ini.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Get product details for cart
     */
    public function getProductForCart(int $productId): ?array
    {
        $product = Product::where('id', $productId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->first();

        if (!$product) {
            return null;
        }

        return [
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => (float) $product->selling_price,
            'cost_price' => (float) ($product->standard_cost ?? $product->purchase_price ?? 0),
            'current_stock' => (float) $product->current_stock,
            'uom' => $product->uom_stock,
            'quantity' => 1,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'notes' => '',
        ];
    }

    /**
     * Calculate cart totals with discount
     */
    public function calculateTotals(array $cartItems, ?int $customerId, float $taxPercentage = 11, float $pointsToUse = 0): array
    {
        // Calculate subtotal
        $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Calculate discount via DiscountEngine
        $discountAmount = 0;
        $discountName = null;
        $discountRule = null;

        try {
            $cartData = [
                'items' => $cartItems,
                'subtotal' => $subtotal,
                'customer_id' => $customerId,
                'tax_percentage' => $taxPercentage,
            ];

            $discountRule = $this->discountEngine->getBestDiscount($cartData);

            if ($discountRule) {
                $discountAmount = (float) $this->discountEngine->calculateDiscountAmount($discountRule, $cartData);
                $discountName = $discountRule->name;
            }
        } catch (\Throwable $e) {
            // Silent fail for discount calculation
        }

        // Calculate customer tier discount (additional)
        $tierDiscount = 0;
        if ($customerId) {
            $customer = Customer::with('tier')->find($customerId);
            if ($customer && $customer->tier && $customer->tier->discount_percentage > 0) {
                $tierDiscount = ($subtotal - $discountAmount) * ($customer->tier->discount_percentage / 100);
            }
        }

        $totalDiscount = $discountAmount + $tierDiscount;

        // Points conversion (1 point = Rp 100)
        $pointsValue = $pointsToUse * 100;
        $maxPointsValue = ($subtotal - $totalDiscount) * 0.5; // Max 50% of subtotal
        $pointsValue = min($pointsValue, $maxPointsValue);

        // Calculate tax
        $taxableAmount = $subtotal - $totalDiscount - $pointsValue;
        $taxAmount = round($taxableAmount * ($taxPercentage / 100), 2);

        // Calculate total
        $total = max(0, $taxableAmount + $taxAmount);

        // Calculate potential points earned (1 point per Rp 10.000)
        $pointsEarned = floor($total / 10000);
        if ($customerId) {
            $customer = Customer::with('tier')->find($customerId);
            if ($customer && $customer->tier) {
                $pointsEarned *= $customer->tier->point_multiplier;
            }
        }

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $totalDiscount,
            'discount_name' => $discountName,
            'tier_discount' => $tierDiscount,
            'points_used' => $pointsToUse,
            'points_value' => $pointsValue,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'points_earned' => (int) $pointsEarned,
        ];
    }

    /**
     * Process complete POS transaction
     *
     * @param array $data [
     *   'cart' => array,
     *   'customer_id' => int|null,
     *   'payment_method' => string,
     *   'paid_amount' => float,
     *   'tax_percentage' => float,
     *   'points_used' => float,
     *   'customer_notes' => string|null,
     * ]
     * @return SalesOrder
     */
    public function processTransaction(array $data): SalesOrder
    {
        // Validate stock first
        $stockValidation = $this->validateStock($data['cart']);
        if (!$stockValidation['valid']) {
            throw new \Exception(implode("\n", $stockValidation['errors']));
        }

        return DB::transaction(function () use ($data) {
            // 1. Calculate totals
            $totals = $this->calculateTotals(
                $data['cart'],
                $data['customer_id'] ?? null,
                $data['tax_percentage'] ?? 11,
                $data['points_used'] ?? 0
            );

            // 2. Create Sales Order
            $order = $this->createSalesOrder($data, $totals);

            // 3. Create Sales Order Items & reduce stock
            $this->createOrderItems($order, $data['cart']);

            // 4. Process stock movements
            $this->processStockMovements($order);

            // 5. Create journal entry for accounting
            $this->createSalesJournal($order);

            // 6. Process customer points
            if ($order->customer_id) {
                $this->processCustomerPoints($order, $data['points_used'] ?? 0);
            }

            // 7. Update customer statistics
            if ($order->customer_id) {
                $this->updateCustomerStats($order);
            }

            return $order->fresh(['items', 'customer']);
        });
    }

    /**
     * Create the sales order record
     */
    protected function createSalesOrder(array $data, array $totals): SalesOrder
    {
        $paidAmount = (float) ($data['paid_amount'] ?? $totals['total']);
        $changeAmount = max(0, $paidAmount - $totals['total']);

        return SalesOrder::create([
            'order_number' => $this->generateOrderNumber(),
            'customer_id' => $data['customer_id'] ?? null,
            'order_date' => now(),
            'order_type' => 'pos',
            'order_channel' => 'store',
            'status' => SalesOrder::STATUS_COMPLETED,
            'subtotal' => $totals['subtotal'],
            'discount_amount' => $totals['discount_amount'],
            'discount_source' => $totals['discount_name'],
            'tax_percentage' => $totals['tax_percentage'],
            'tax_amount' => $totals['tax_amount'],
            'points_used' => $totals['points_value'],
            'points_earned' => $totals['points_earned'],
            'total' => $totals['total'],
            'payment_status' => SalesOrder::PAYMENT_PAID,
            'payment_method' => $data['payment_method'] ?? 'cash',
            'paid_amount' => $paidAmount,
            'change_amount' => $changeAmount,
            'customer_notes' => $data['customer_notes'] ?? null,
            'served_by' => Auth::id(),
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Create sales order items with cost tracking
     */
    protected function createOrderItems(SalesOrder $order, array $cartItems): void
    {
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['price'];
            $costPrice = (float) ($product->standard_cost ?? $product->purchase_price ?? 0);

            $subtotal = $quantity * $unitPrice;
            $discountAmount = (float) ($item['discount_amount'] ?? 0);
            $total = $subtotal - $discountAmount;
            $profit = $total - ($costPrice * $quantity);

            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'uom' => $item['uom'] ?? $product->uom_stock,
                'unit_price' => $unitPrice,
                'discount_percentage' => $item['discount_percentage'] ?? 0,
                'discount_amount' => $discountAmount,
                'tax_percentage' => $order->tax_percentage,
                'tax_amount' => $total * ($order->tax_percentage / 100),
                'subtotal' => $subtotal,
                'total' => $total,
                'cost_price' => $costPrice,
                'profit' => $profit,
                'notes' => $item['notes'] ?? null,
                'fulfillment_status' => 'delivered',
            ]);
        }
    }

    /**
     * Process stock movements (reduce inventory)
     */
    protected function processStockMovements(SalesOrder $order): void
    {
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if (!$product)
                continue;

            // Calculate new balance
            $newBalance = (float) $product->current_stock - (float) $item->quantity;

            // Create stock movement record
            StockMovement::create([
                'type' => StockMovement::TYPE_OUT,
                'reference_type' => SalesOrder::class,
                'reference_id' => $order->id,
                'reference_number' => $order->order_number,
                'product_id' => $product->id,
                'quantity' => -abs($item->quantity), // Negative for OUT
                'uom' => $item->uom ?? $product->uom_stock,
                'balance_after' => $newBalance,
                'warehouse_code' => 'MAIN',
                'notes' => "Penjualan POS #{$order->order_number}",
                'created_by' => Auth::id(),
            ]);

            // Update product stock
            $product->current_stock = $newBalance;
            $product->save();
        }
    }

    /**
     * Create journal entry for sales transaction
     *
     * Debit: Cash/Bank (Asset) - Total received
     * Debit: Discount (Expense) - If any discount
     * Credit: Sales Revenue (Income) - Subtotal
     * Credit: Tax Payable (Liability) - Tax amount
     *
     * For COGS:
     * Debit: Cost of Goods Sold (Expense)
     * Credit: Inventory (Asset)
     */
    protected function createSalesJournal(SalesOrder $order): void
    {
        // Get default accounts
        $cashAccountId = ChartOfAccount::where('code', 'like', '1-1%')
            ->where('type', 'asset')
            ->where('is_active', true)
            ->first()?->id;

        $salesAccountId = ChartOfAccount::where('code', 'like', '4-1%')
            ->where('type', 'revenue')
            ->where('is_active', true)
            ->first()?->id;

        $taxPayableAccountId = ChartOfAccount::where('code', 'like', '2-1%')
            ->where('subtype', 'like', '%tax%')
            ->where('is_active', true)
            ->first()?->id;

        $cogsAccountId = ChartOfAccount::where('code', 'like', '5-1%')
            ->where('type', 'expense')
            ->where('is_active', true)
            ->first()?->id;

        // If accounts not found, skip journal creation
        if (!$cashAccountId || !$salesAccountId) {
            return;
        }

        // Calculate total COGS
        $totalCogs = $order->items->sum(fn($item) => $item->cost_price * $item->quantity);

        $postings = [];

        // Debit: Cash received
        $postings[] = [
            'account_id' => $cashAccountId,
            'debit' => $order->total,
            'credit' => 0,
            'description' => "Penerimaan kas dari penjualan {$order->order_number}",
        ];

        // Credit: Sales revenue (subtotal before tax)
        $salesAmount = $order->subtotal - $order->discount_amount;
        $postings[] = [
            'account_id' => $salesAccountId,
            'debit' => 0,
            'credit' => $salesAmount,
            'description' => "Pendapatan penjualan {$order->order_number}",
        ];

        // Credit: Tax payable
        if ($order->tax_amount > 0 && $taxPayableAccountId) {
            $postings[] = [
                'account_id' => $taxPayableAccountId,
                'debit' => 0,
                'credit' => $order->tax_amount,
                'description' => "PPN penjualan {$order->order_number}",
            ];
        }

        // Create sales journal
        try {
            $this->journalService->createJournalEntry([
                'posting_date' => $order->order_date,
                'description' => "Penjualan POS {$order->order_number}" .
                    ($order->customer ? " - {$order->customer->name}" : " - Walk-in Customer"),
                'referenceable' => $order,
                'postings' => $postings,
            ], true);
        } catch (\Exception $e) {
            // Log but don't fail transaction
            Log::warning("Failed to create sales journal for {$order->order_number}: " . $e->getMessage());
        }

        // Create COGS journal if we have inventory accounts
        if ($totalCogs > 0 && $cogsAccountId) {
            $cogsPostings = [];

            // Debit: COGS
            $cogsPostings[] = [
                'account_id' => $cogsAccountId,
                'debit' => $totalCogs,
                'credit' => 0,
                'description' => "HPP penjualan {$order->order_number}",
            ];

            // Credit: Inventory for each product
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product && $product->inventory_account_id) {
                    $itemCogs = $item->cost_price * $item->quantity;
                    if ($itemCogs > 0) {
                        $cogsPostings[] = [
                            'account_id' => $product->inventory_account_id,
                            'debit' => 0,
                            'credit' => $itemCogs,
                            'description' => "Pengeluaran persediaan {$product->name}",
                        ];
                    }
                }
            }

            // Only create if we have at least 2 postings (debit and credit)
            if (count($cogsPostings) >= 2) {
                try {
                    $this->journalService->createJournalEntry([
                        'posting_date' => $order->order_date,
                        'description' => "HPP Penjualan {$order->order_number}",
                        'referenceable' => $order,
                        'postings' => $cogsPostings,
                    ], true);
                } catch (\Exception $e) {
                    Log::warning("Failed to create COGS journal for {$order->order_number}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Process customer points (earn & redeem)
     */
    protected function processCustomerPoints(SalesOrder $order, float $pointsUsed): void
    {
        $customer = Customer::find($order->customer_id);
        if (!$customer)
            return;

        // Redeem points if used
        if ($pointsUsed > 0) {
            try {
                $customer->redeemPoints($pointsUsed, $order, "Penukaran poin untuk order {$order->order_number}");
            } catch (\Exception $e) {
                Log::warning("Failed to redeem points for order {$order->order_number}: " . $e->getMessage());
            }
        }

        // Earn points from transaction
        if ($order->points_earned > 0) {
            $customer->earnPoints((float) $order->total, $order, "Poin dari pembelian {$order->order_number}");
        }
    }

    /**
     * Update customer statistics after transaction
     */
    protected function updateCustomerStats(SalesOrder $order): void
    {
        $customer = Customer::find($order->customer_id);
        if (!$customer)
            return;

        $customer->increment('total_spent', (float) $order->total);
        $customer->increment('transaction_count');
        $customer->update(['last_purchase_date' => $order->order_date]);

        // Check if customer should be upgraded to new tier
        $newTier = \App\Models\CustomerTier::getTierForSpending((float) $customer->total_spent);
        if ($newTier && $newTier->id !== $customer->customer_tier_id) {
            $customer->update(['customer_tier_id' => $newTier->id]);
        }
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods(): array
    {
        return [
            'cash' => [
                'code' => 'cash',
                'name' => 'Tunai',
                'icon' => 'banknotes',
                'requires_reference' => false,
            ],
            'debit_card' => [
                'code' => 'debit_card',
                'name' => 'Kartu Debit',
                'icon' => 'credit-card',
                'requires_reference' => true,
            ],
            'credit_card' => [
                'code' => 'credit_card',
                'name' => 'Kartu Kredit',
                'icon' => 'credit-card',
                'requires_reference' => true,
            ],
            'e_wallet' => [
                'code' => 'e_wallet',
                'name' => 'E-Wallet',
                'icon' => 'device-phone-mobile',
                'requires_reference' => true,
            ],
            'qris' => [
                'code' => 'qris',
                'name' => 'QRIS',
                'icon' => 'qr-code',
                'requires_reference' => true,
            ],
            'bank_transfer' => [
                'code' => 'bank_transfer',
                'name' => 'Transfer Bank',
                'icon' => 'building-library',
                'requires_reference' => true,
            ],
        ];
    }

    /**
     * Cancel/void a POS transaction
     */
    public function cancelTransaction(SalesOrder $order, string $reason): SalesOrder
    {
        if ($order->status === SalesOrder::STATUS_CANCELLED) {
            throw new \Exception("Order sudah dibatalkan sebelumnya.");
        }

        return DB::transaction(function () use ($order, $reason) {
            // 1. Reverse stock movements
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if (!$product)
                    continue;

                // Return stock
                $newBalance = (float) $product->current_stock + (float) $item->quantity;

                // Create reversal stock movement
                StockMovement::create([
                    'type' => StockMovement::TYPE_IN,
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'reference_number' => $order->order_number,
                    'product_id' => $product->id,
                    'quantity' => abs($item->quantity),
                    'uom' => $item->uom ?? $product->uom_stock,
                    'balance_after' => $newBalance,
                    'warehouse_code' => 'MAIN',
                    'notes' => "Pembatalan POS #{$order->order_number}",
                    'created_by' => Auth::id(),
                ]);

                $product->current_stock = $newBalance;
                $product->save();
            }

            // 2. Reverse customer points
            if ($order->customer_id && $order->points_earned > 0) {
                $customer = Customer::find($order->customer_id);
                if ($customer) {
                    CustomerPointsLedger::create([
                        'customer_id' => $customer->id,
                        'type' => CustomerPointsLedger::TYPE_ADJUST,
                        'points' => -$order->points_earned,
                        'balance_after' => max(0, $customer->total_points - $order->points_earned),
                        'referenceable_type' => SalesOrder::class,
                        'referenceable_id' => $order->id,
                        'reference_number' => $order->order_number,
                        'description' => "Pembatalan poin dari order {$order->order_number}",
                        'created_by' => Auth::id(),
                    ]);
                    $customer->decrement('total_points', (float) $order->points_earned);
                }
            }

            // 3. Return redeemed points
            if ($order->customer_id && $order->points_used > 0) {
                $pointsToReturn = $order->points_used / 100; // Convert back from rupiah
                $customer = Customer::find($order->customer_id);
                if ($customer) {
                    CustomerPointsLedger::create([
                        'customer_id' => $customer->id,
                        'type' => CustomerPointsLedger::TYPE_ADJUST,
                        'points' => $pointsToReturn,
                        'balance_after' => $customer->total_points + $pointsToReturn,
                        'referenceable_type' => SalesOrder::class,
                        'referenceable_id' => $order->id,
                        'reference_number' => $order->order_number,
                        'description' => "Pengembalian poin dari pembatalan order {$order->order_number}",
                        'created_by' => Auth::id(),
                    ]);
                    $customer->increment('total_points', $pointsToReturn);
                }
            }

            // 4. Update order status
            $order->update([
                'status' => SalesOrder::STATUS_CANCELLED,
                'payment_status' => SalesOrder::PAYMENT_REFUNDED,
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // 5. Update customer stats
            if ($order->customer_id) {
                $customer = Customer::find($order->customer_id);
                if ($customer) {
                    $customer->decrement('total_spent', (float) $order->total);
                    $customer->decrement('transaction_count');
                }
            }

            return $order->fresh();
        });
    }
}
