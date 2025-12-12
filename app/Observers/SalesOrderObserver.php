<?php

namespace App\Observers;

use App\Models\SalesOrder;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\JournalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrderObserver
{
    /**
     * Handle the SalesOrder "creating" event.
     */
    public function creating(SalesOrder $salesOrder): void
    {
        // Auto-generate order number jika belum ada
        if (empty($salesOrder->order_number)) {
            $salesOrder->order_number = $this->generateOrderNumber();
        }

        // Set order_date jika belum ada
        if (empty($salesOrder->order_date)) {
            $salesOrder->order_date = now();
        }

        // Set default tax percentage (11% PPN)
        if (empty($salesOrder->tax_percentage)) {
            $salesOrder->tax_percentage = 11;
        }

        // Set created_by
        if (Auth::check() && empty($salesOrder->created_by)) {
            $salesOrder->created_by = Auth::id();
        }
    }

    /**
     * Handle the SalesOrder "updating" event.
     */
    public function updating(SalesOrder $salesOrder): void
    {
        // Set updated_by
        if (Auth::check()) {
            $salesOrder->updated_by = Auth::id();
        }

        // Detect status change to CONFIRMED (only for non-POS orders)
        // POS orders are already COMPLETED immediately and handled by PosService
        if ($salesOrder->isDirty('status') && $salesOrder->status === SalesOrder::STATUS_CONFIRMED) {
            // Only process if NOT coming from POS (POS uses order_type = 'pos')
            if ($salesOrder->order_type !== 'pos') {
                $this->handleConfirmed($salesOrder);
            }
        }

        // Detect status change to COMPLETED (only for non-POS orders)
        if ($salesOrder->isDirty('status') && $salesOrder->status === SalesOrder::STATUS_COMPLETED) {
            // Only process if NOT coming from POS (POS already handles this in PosService)
            if ($salesOrder->order_type !== 'pos') {
                $this->handleCompleted($salesOrder);
            }
        }

        // Detect status change to CANCELLED
        if ($salesOrder->isDirty('status') && $salesOrder->status === SalesOrder::STATUS_CANCELLED) {
            if (Auth::check()) {
                $salesOrder->cancelled_by = Auth::id();
                $salesOrder->cancelled_at = now();
            }
        }
    }

    /**
     * Handle the SalesOrder "deleted" event.
     */
    public function deleted(SalesOrder $salesOrder): void
    {
        //
    }

    /**
     * Handle the SalesOrder "restored" event.
     */
    public function restored(SalesOrder $salesOrder): void
    {
        //
    }

    /**
     * Handle the SalesOrder "force deleted" event.
     */
    public function forceDeleted(SalesOrder $salesOrder): void
    {
        //
    }

    /**
     * Handle when sales order is CONFIRMED
     * - Decrease stock
     * - Create stock movements
     */
    private function handleConfirmed(SalesOrder $salesOrder): void
    {
        DB::transaction(function () use ($salesOrder) {
            foreach ($salesOrder->items as $item) {
                $product = $item->product;

                // Validate stock availability
                if ($product->current_stock < $item->quantity) {
                    throw new \Exception(
                        "Stok tidak cukup untuk {$product->name}. " .
                        "Tersedia: {$product->current_stock} {$item->uom}, " .
                        "Dibutuhkan: {$item->quantity} {$item->uom}"
                    );
                }

                // Decrease stock
                $oldStock = $product->current_stock;
                $product->current_stock -= $item->quantity;
                $product->save();

                // Create stock movement
                StockMovement::create([
                    'type' => StockMovement::TYPE_OUT,
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $salesOrder->id,
                    'reference_number' => $salesOrder->order_number,
                    'product_id' => $product->id,
                    'quantity' => -$item->quantity, // Negative untuk keluar
                    'uom' => $item->uom,
                    'balance_after' => $product->current_stock,
                    'notes' => "Penjualan ke " . ($salesOrder->customer?->name ?? 'Walk-in Customer'),
                    'created_by' => Auth::id(),
                ]);

                Log::info("Stock decreased for SO {$salesOrder->order_number}: {$product->name} from {$oldStock} to {$product->current_stock}");
            }
        });
    }

    /**
     * Handle when sales order is COMPLETED
     * - Create journal entry for revenue & COGS
     * - Earn customer points
     */
    private function handleCompleted(SalesOrder $salesOrder): void
    {
        DB::transaction(function () use ($salesOrder) {
            // 1. Create Journal Entry
            $this->createJournalEntry($salesOrder);

            // 2. Earn customer points (if customer exists)
            if ($salesOrder->customer_id && $salesOrder->total > 0) {
                $this->earnCustomerPoints($salesOrder);
            }

            // 3. Update customer statistics
            if ($salesOrder->customer) {
                $salesOrder->customer->updateTotalSpent($salesOrder->total);
            }
        });
    }

    /**
     * Create journal entry for sales
     */
    private function createJournalEntry(SalesOrder $salesOrder): void
    {
        $journalService = app(JournalService::class);

        // Calculate total COGS dari semua items
        $totalCogs = $salesOrder->items->sum(function ($item) {
            return ($item->cost_price ?? 0) * $item->quantity;
        });

        $postings = [];

        // 1. Debit: Kas/Piutang (depending on payment status)
        if ($salesOrder->payment_status === SalesOrder::PAYMENT_PAID) {
            // Kas - tergantung payment method
            $cashAccountCode = match ($salesOrder->payment_method) {
                'cash' => '1-1100', // Kas
                'bank_transfer' => '1-1200', // Bank
                'credit_card', 'debit_card' => '1-1200', // Bank
                'e_wallet', 'qris' => '1-1210', // E-Money/E-Wallet
                default => '1-1100', // Default Kas
            };

            $postings[] = [
                'account_code' => $cashAccountCode,
                'debit' => $salesOrder->total,
                'credit' => 0,
                'description' => "Penerimaan pembayaran {$salesOrder->order_number}",
            ];
        } else {
            // Piutang Usaha
            $postings[] = [
                'account_code' => '1-1300', // Piutang Usaha
                'debit' => $salesOrder->total,
                'credit' => 0,
                'description' => "Piutang penjualan {$salesOrder->order_number}",
            ];
        }

        // 2. Credit: Pendapatan Penjualan
        $postings[] = [
            'account_code' => '4-1100', // Pendapatan Penjualan Roti (default)
            'debit' => 0,
            'credit' => $salesOrder->subtotal - $salesOrder->discount_amount,
            'description' => "Penjualan {$salesOrder->order_number}",
        ];

        // 3. Credit: Pendapatan PPN (if any)
        if ($salesOrder->tax_amount > 0) {
            $postings[] = [
                'account_code' => '2-1200', // Hutang PPN
                'debit' => 0,
                'credit' => $salesOrder->tax_amount,
                'description' => "PPN {$salesOrder->order_number}",
            ];
        }

        // 4. Debit: HPP (Cost of Goods Sold)
        if ($totalCogs > 0) {
            $postings[] = [
                'account_code' => '5-1100', // HPP
                'debit' => $totalCogs,
                'credit' => 0,
                'description' => "HPP {$salesOrder->order_number}",
            ];

            // 5. Credit: Persediaan Barang Jadi
            $postings[] = [
                'account_code' => '1-1330', // Persediaan Barang Jadi
                'debit' => 0,
                'credit' => $totalCogs,
                'description' => "Pengurangan stok {$salesOrder->order_number}",
            ];
        }

        try {
            $journalService->createJournalEntry([
                'posting_date' => $salesOrder->order_date,
                'description' => "Penjualan: {$salesOrder->order_number} - " . ($salesOrder->customer?->name ?? 'Walk-in'),
                'referenceable' => $salesOrder,
                'postings' => $postings,
            ], autoPost: true);

            Log::info("Journal entry created for Sales Order {$salesOrder->order_number}");
        } catch (\Exception $e) {
            Log::error("Failed to create journal for Sales Order {$salesOrder->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Earn customer loyalty points
     */
    private function earnCustomerPoints(SalesOrder $salesOrder): void
    {
        try {
            $customer = $salesOrder->customer;

            // Earn points berdasarkan total amount
            $pointsLedger = $customer->earnPoints(
                amount: $salesOrder->total,
                reference: $salesOrder,
                description: "Poin dari pembelian {$salesOrder->order_number}"
            );

            // Update points_earned di sales order
            $salesOrder->update([
                'points_earned' => $pointsLedger->points
            ]);

            Log::info("Customer {$customer->name} earned {$pointsLedger->points} points from SO {$salesOrder->order_number}");
        } catch (\Exception $e) {
            Log::error("Failed to earn points for SO {$salesOrder->order_number}: " . $e->getMessage());
            // Don't throw - points earning failure shouldn't block sales
        }
    }

    /**
     * Generate unique order number: SO-YYYYMM-XXXX
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'SO-' . date('Ym') . '-';

        // Get latest order number for this month
        $latestOrder = SalesOrder::withTrashed()
            ->where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($latestOrder) {
            // Extract sequence number and increment
            $lastSequence = (int) substr($latestOrder->order_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            // First order of the month
            $newSequence = 1;
        }

        // Format: SO-202512-0001
        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
