<?php

namespace App\Livewire\Pos;

use App\Models\Product;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Services\PosService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

class PointOfSale extends Component
{
    // Cart & Customer
    public array $cart = [];
    public ?int $selectedCustomerId = null;
    public ?array $selectedCustomer = null;
    public string $searchProduct = '';
    public string $searchCustomer = '';

    // Calculations
    public float $subtotal = 0;
    public float $discountAmount = 0;
    public ?string $discountName = null;
    public float $tierDiscount = 0;
    public float $taxPercentage = 11;
    public float $taxAmount = 0;
    public float $total = 0;

    // Points
    public float $customerPoints = 0;
    public float $pointsToUse = 0;
    public float $pointsValue = 0;
    public int $pointsEarned = 0;

    // Payment
    public string $paymentMethod = 'cash';
    public float $paidAmount = 0;
    public float $changeAmount = 0;
    public string $paymentReference = '';

    // UI State
    public bool $showReceipt = false;
    public bool $showPaymentModal = false;
    public bool $showCustomerModal = false;
    public bool $showClearCartModal = false;
    public bool $showCartPanel = true;
    public ?int $lastOrderId = null;
    public ?string $lastOrderNumber = null;
    public array $receipt = [];
    public array $stockWarnings = [];
    public array $stockErrors = [];
    public bool $isFullscreen = false;

    // Payment Methods
    public array $paymentMethods = [];

    // Discount & Birthday Info
    public array $activeDiscounts = [];
    public array $productDiscounts = [];
    public array $birthdayCustomers = [];
    public array $customerDiscounts = [];
    public bool $isCustomerBirthday = false;

    protected PosService $posService;

    public function boot(PosService $posService)
    {
        $this->posService = $posService;
        $this->paymentMethods = $posService->getPaymentMethods();
    }

    public function mount($fullscreen = false)
    {
        $this->isFullscreen = $fullscreen;

        // Load active discounts on mount
        try {
            $this->loadDiscountInfo();
        } catch (\Exception $e) {
            // If discount loading fails, use empty arrays
            $this->activeDiscounts = [];
            $this->productDiscounts = [];
            $this->birthdayCustomers = [];
        }
    }

    /**
     * Load discount information
     */
    public function loadDiscountInfo(): void
    {
        try {
            $this->activeDiscounts = $this->posService->getActiveDiscountsToday()->toArray();
            $this->productDiscounts = $this->posService->getProductsWithDiscounts();
            $this->birthdayCustomers = $this->posService->getBirthdayCustomersToday()->toArray();
        } catch (\Exception $e) {
            $this->activeDiscounts = [];
            $this->productDiscounts = [];
            $this->birthdayCustomers = [];
        }
    }

    public function render()
    {
        // Ensure variables have default values
        $products = collect();
        $customers = collect();

        try {
            $products = Product::query()
                ->where('is_active', true)
                ->where('is_sellable', true)
                ->when($this->searchProduct, function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'like', "%{$this->searchProduct}%")
                            ->orWhere('sku', 'like', "%{$this->searchProduct}%")
                            ->orWhere('barcode', $this->searchProduct);
                    });
                })
                ->orderBy('name')
                ->limit(100)
                ->get();
        } catch (\Exception $e) {
            Log::error('POS products query error: ' . $e->getMessage());
            $products = collect();
        }

        try {
            $customers = Customer::query()
                ->where('is_active', true)
                ->when($this->searchCustomer, function ($q) {
                    $q->where(function ($query) {
                        $query->where('name', 'like', "%{$this->searchCustomer}%")
                            ->orWhere('phone', 'like', "%{$this->searchCustomer}%")
                            ->orWhere('customer_code', 'like', "%{$this->searchCustomer}%");
                    });
                })
                ->with('tier')
                ->orderBy('name')
                ->limit(50)
                ->get();

            // Add birthday flag to customers
            $today = Carbon::now();
            $customers = $customers->map(function ($customer) use ($today) {
                $customer->is_birthday = false;
                if ($customer->date_of_birth) {
                    $birthday = Carbon::parse($customer->date_of_birth);
                    $customer->is_birthday = ($today->month === $birthday->month && $today->day === $birthday->day);
                }
                return $customer;
            });
        } catch (\Exception $e) {
            Log::error('POS customers query error: ' . $e->getMessage());
            $customers = collect();
        }

        return view('livewire.pos.point-of-sale', [
            'products' => $products,
            'customers' => $customers,
        ]);
    }

    /**
     * Add product to cart
     */
    public function addToCart(int $productId): void
    {
        $productData = $this->posService->getProductForCart($productId);

        if (!$productData) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Produk tidak ditemukan atau tidak dapat dijual.'
            ]);
            return;
        }

        // Check if product already in cart
        $existingIndex = collect($this->cart)->search(fn($item) => $item['product_id'] === $productId);

        if ($existingIndex !== false) {
            // Check stock before incrementing
            $newQty = $this->cart[$existingIndex]['quantity'] + 1;
            if ($newQty > $productData['current_stock']) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "Stok {$productData['name']} tidak mencukupi. Tersedia: {$productData['current_stock']}"
                ]);
                return;
            }
            $this->cart[$existingIndex]['quantity'] = $newQty;
        } else {
            // Check if stock available
            if ($productData['current_stock'] < 1) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "Stok {$productData['name']} habis!"
                ]);
                return;
            }
            $this->cart[] = $productData;
        }

        $this->calculateTotals();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$productData['name']} ditambahkan ke keranjang"
        ]);
    }

    /**
     * Add product by barcode scan
     */
    public function addByBarcode(string $barcode): void
    {
        $product = Product::where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->first();

        if ($product) {
            $this->addToCart($product->id);
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Produk dengan barcode {$barcode} tidak ditemukan."
            ]);
        }
    }

    /**
     * Update item quantity in cart
     */
    public function updateQuantity(int $index, $quantity): void
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        $quantity = max(1, (int) $quantity);
        $product = Product::find($this->cart[$index]['product_id']);

        if ($product && $quantity > $product->current_stock) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Stok {$product->name} tidak mencukupi. Tersedia: {$product->current_stock}"
            ]);
            $quantity = (int) $product->current_stock;
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->calculateTotals();
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $index): void
    {
        if (!isset($this->cart[$index])) {
            return;
        }

        $name = $this->cart[$index]['name'];
        array_splice($this->cart, $index, 1);
        $this->calculateTotals();

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => "{$name} dihapus dari keranjang"
        ]);
    }

    /**
     * Show clear cart confirmation modal
     */
    public function confirmClearCart(): void
    {
        $this->showClearCartModal = true;
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): void
    {
        $this->cart = [];
        $this->resetCalculations();
        $this->showClearCartModal = false;
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Keranjang telah dikosongkan'
        ]);
    }

    /**
     * Toggle cart panel visibility
     */
    public function toggleCartPanel(): void
    {
        $this->showCartPanel = !$this->showCartPanel;
    }

    /**
     * Select customer
     */
    public function selectCustomer(?int $customerId): void
    {
        $this->selectedCustomerId = $customerId;

        if ($customerId) {
            $customer = Customer::with('tier')->find($customerId);
            if ($customer) {
                // Check if birthday
                $this->isCustomerBirthday = $this->posService->isCustomerBirthday($customerId);

                // Get customer discounts
                $this->customerDiscounts = $this->posService->getCustomerDiscountInfo($customerId);

                $this->selectedCustomer = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'tier_name' => $customer->tier?->name ?? 'Regular',
                    'tier_color' => $customer->tier?->color ?? 'gray',
                    'total_points' => (float) $customer->total_points,
                    'discount_percentage' => (float) ($customer->tier?->discount_percentage ?? 0),
                    'is_birthday' => $this->isCustomerBirthday,
                    'discounts' => $this->customerDiscounts,
                ];
                $this->customerPoints = (float) $customer->total_points;
            }
        } else {
            $this->selectedCustomer = null;
            $this->customerPoints = 0;
            $this->pointsToUse = 0;
            $this->isCustomerBirthday = false;
            $this->customerDiscounts = [];
        }

        $this->showCustomerModal = false;
        $this->calculateTotals();
    }

    /**
     * Remove selected customer
     */
    public function removeCustomer(): void
    {
        $this->selectedCustomerId = null;
        $this->selectedCustomer = null;
        $this->customerPoints = 0;
        $this->pointsToUse = 0;
        $this->calculateTotals();
    }

    /**
     * Set points to use
     */
    public function setPointsToUse(float $points): void
    {
        // Validate points
        $points = max(0, min($points, $this->customerPoints));
        $this->pointsToUse = $points;
        $this->calculateTotals();
    }

    /**
     * Use all available points
     */
    public function useAllPoints(): void
    {
        $this->setPointsToUse($this->customerPoints);
    }

    /**
     * Reset points usage
     */
    public function resetPoints(): void
    {
        $this->pointsToUse = 0;
        $this->calculateTotals();
    }

    /**
     * Calculate all totals using PosService
     */
    public function calculateTotals(): void
    {
        if (empty($this->cart)) {
            $this->resetCalculations();
            return;
        }

        $totals = $this->posService->calculateTotals(
            $this->cart,
            $this->selectedCustomerId,
            $this->taxPercentage,
            $this->pointsToUse
        );

        $this->subtotal = $totals['subtotal'];
        $this->discountAmount = $totals['discount_amount'];
        $this->discountName = $totals['discount_name'];
        $this->tierDiscount = $totals['tier_discount'];
        $this->pointsValue = $totals['points_value'];
        $this->taxAmount = $totals['tax_amount'];
        $this->total = $totals['total'];
        $this->pointsEarned = $totals['points_earned'];

        // Validate stock
        $validation = $this->posService->validateStock($this->cart);
        $this->stockErrors = $validation['errors'];
        $this->stockWarnings = $validation['warnings'];

        // Reset change calculation
        $this->calculateChange();
    }

    /**
     * Reset calculations
     */
    protected function resetCalculations(): void
    {
        $this->subtotal = 0;
        $this->discountAmount = 0;
        $this->discountName = null;
        $this->tierDiscount = 0;
        $this->taxAmount = 0;
        $this->total = 0;
        $this->pointsValue = 0;
        $this->pointsEarned = 0;
        $this->stockErrors = [];
        $this->stockWarnings = [];
        $this->paidAmount = 0;
        $this->changeAmount = 0;
    }

    /**
     * Set payment method
     */
    public function setPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
        $this->paymentReference = '';

        // Auto-fill paid amount for non-cash methods
        if ($method !== 'cash') {
            $this->paidAmount = $this->total;
            $this->calculateChange();
        }
    }

    /**
     * Set paid amount
     */
    public function setPaidAmount(float $amount): void
    {
        $this->paidAmount = max(0, $amount);
        $this->calculateChange();
    }

    /**
     * Quick cash buttons
     */
    public function quickCash(int $amount): void
    {
        $this->paidAmount = (float) $amount;
        $this->calculateChange();
    }

    /**
     * Exact amount
     */
    public function exactAmount(): void
    {
        $this->paidAmount = $this->total;
        $this->calculateChange();
    }

    /**
     * Calculate change
     */
    public function calculateChange(): void
    {
        $this->changeAmount = max(0, $this->paidAmount - $this->total);
    }

    /**
     * Open payment modal
     */
    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Keranjang masih kosong!'
            ]);
            return;
        }

        if (!empty($this->stockErrors)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Ada masalah dengan stok. Silakan periksa keranjang.'
            ]);
            return;
        }

        // Calculate totals first
        $this->calculateTotals();

        // Default paid amount for cash
        if ($this->paymentMethod === 'cash') {
            $this->paidAmount = 0;
        } else {
            $this->paidAmount = $this->total;
        }

        $this->showPaymentModal = true;
    }

    /**
     * Close payment modal
     */
    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
    }

    /**
     * Process the payment
     */
    public function processPayment(): void
    {
        // Validation
        if ($this->total <= 0 || empty($this->cart)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Tidak ada item untuk diproses!'
            ]);
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->paidAmount < $this->total) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Jumlah pembayaran kurang dari total!'
            ]);
            return;
        }

        if (!empty($this->stockErrors)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => implode("\n", $this->stockErrors)
            ]);
            return;
        }

        try {
            $order = $this->posService->processTransaction([
                'cart' => $this->cart,
                'customer_id' => $this->selectedCustomerId,
                'payment_method' => $this->paymentMethod,
                'paid_amount' => $this->paidAmount,
                'tax_percentage' => $this->taxPercentage,
                'points_used' => $this->pointsToUse,
                'payment_reference' => $this->paymentReference,
            ]);

            // Set receipt data
            $this->lastOrderId = $order->id;
            $this->lastOrderNumber = $order->order_number;
            $this->receipt = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer?->name ?? 'Walk-in Customer',
                'items' => $order->items->map(fn($item) => [
                    'name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                    'total' => $item->total,
                ])->toArray(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'discount_source' => $order->discount_source,
                'tax_amount' => $order->tax_amount,
                'points_used' => $order->points_used,
                'points_earned' => $order->points_earned,
                'total' => $order->total,
                'paid_amount' => $order->paid_amount,
                'change_amount' => $order->change_amount,
                'payment_method' => $this->paymentMethods[$order->payment_method]['name'] ?? $order->payment_method,
                'cashier' => Auth::user()->name,
                'date' => $order->order_date->format('d/m/Y H:i'),
            ];

            // Show receipt and reset
            $this->showPaymentModal = false;
            $this->showReceipt = true;
            $this->resetCart();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Transaksi berhasil! No. Order: {$order->order_number}"
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset cart after successful transaction
     */
    public function resetCart(): void
    {
        $this->cart = [];
        $this->selectedCustomerId = null;
        $this->selectedCustomer = null;
        $this->customerPoints = 0;
        $this->pointsToUse = 0;
        $this->paidAmount = 0;
        $this->changeAmount = 0;
        $this->paymentMethod = 'cash';
        $this->paymentReference = '';
        $this->resetCalculations();
    }

    /**
     * Close receipt and start new transaction
     */
    public function closeReceipt(): void
    {
        $this->showReceipt = false;
        $this->lastOrderId = null;
        $this->lastOrderNumber = null;
        $this->receipt = [];
    }

    /**
     * Print receipt
     */
    public function printReceipt(): void
    {
        $this->dispatch('print-receipt');
    }

    /**
     * Get total items in cart
     */
    public function getTotalItemsProperty(): int
    {
        return count($this->cart);
    }

    /**
     * Get total quantity in cart
     */
    public function getTotalQuantityProperty(): int
    {
        return (int) collect($this->cart)->sum('quantity');
    }

    /**
     * Check if can checkout
     */
    public function getCanCheckoutProperty(): bool
    {
        return !empty($this->cart) && empty($this->stockErrors) && $this->total > 0;
    }
}
