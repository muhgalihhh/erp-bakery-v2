<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\JournalService;

class GoodReceipt extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'purchase_order_id',
        'received_by',
        'receipt_number',
        'receipt_date',
        'status',
        'notes',
        'delivery_note_number',
    ];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    /**
     * Get the purchase order
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get receiver user
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get all items
     */
    public function items(): HasMany
    {
        return $this->hasMany(GoodReceiptItem::class);
    }

    /**
     * Check if PO is fully received (based on all confirmed GRs)
     */
    public function isFullyReceived(): bool
    {
        $po = $this->purchaseOrder;

        foreach ($po->items as $poItem) {
            // Get total confirmed received for this item across all GRs
            $totalReceived = GoodReceiptItem::whereHas('goodReceipt', function ($q) use ($po) {
                $q->where('purchase_order_id', $po->id)
                    ->where('status', self::STATUS_CONFIRMED);
            })
                ->where('purchase_order_item_id', $poItem->id)
                ->sum('received_quantity');

            // If any item is not fully received, return false
            if ($totalReceived < $poItem->quantity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Confirm receipt - update PO status and inventory
     */
    public function confirm(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return;
        }

        $this->status = self::STATUS_CONFIRMED;
        $this->save();

        // Update inventory stock for each item
        foreach ($this->items as $item) {
            $acceptedQty = $item->received_quantity - $item->rejected_quantity;
            if ($acceptedQty > 0) {
                // Increase stock with auto-conversion from purchase UOM to stock UOM
                // e.g., 10 Sak → 10 × 25 = 250 Kg
                $item->product->increaseStock($acceptedQty, 'purchase');
            }
        }

        // Update PO status based on receiving completion
        if ($this->isFullyReceived()) {
            $this->purchaseOrder->status = PurchaseOrder::STATUS_RECEIVED;
        } else {
            // Check if any item has been received (partial)
            $hasReceivedAny = GoodReceiptItem::whereHas('goodReceipt', function ($q) {
                $q->where('purchase_order_id', $this->purchase_order_id)
                    ->where('status', self::STATUS_CONFIRMED);
            })
                ->exists();

            if ($hasReceivedAny) {
                $this->purchaseOrder->status = PurchaseOrder::STATUS_PARTIALLY_RECEIVED;
            } else {
                $this->purchaseOrder->status = PurchaseOrder::STATUS_APPROVED;
            }
        }
        $this->purchaseOrder->save();

        // AUTO-POSTING JOURNAL ENTRY (sesuai arsitektur dokumen)
        // Jurnal: Debit Persediaan (Asset), Kredit Hutang Usaha (Liability)
        $this->postJournalEntry();
    }

    /**
     * Post journal entry untuk Good Receipt
     *
     * Logika Akuntansi:
     * - Debit: Persediaan Bahan Baku (Asset bertambah)
     * - Kredit: Hutang Usaha ke Vendor (Liability bertambah)
     */
    protected function postJournalEntry(): void
    {
        $journalService = app(JournalService::class);

        // Hitung total nilai barang diterima
        $totalValue = 0;
        $postings = [];

        foreach ($this->items as $item) {
            $acceptedQty = $item->received_quantity - $item->rejected_quantity;
            if ($acceptedQty <= 0)
                continue;

            $lineValue = $acceptedQty * $item->unit_price;
            $totalValue += $lineValue;

            // Dapatkan inventory account dari produk
            $inventoryAccountId = $item->product->inventory_account_id;

            if (!$inventoryAccountId) {
                \Log::warning("Product {$item->product->name} tidak punya inventory_account_id! Skip journal posting.");
                continue;
            }

            // Debit: Persediaan per produk
            $postings[] = [
                'account_id' => $inventoryAccountId,
                'debit' => $lineValue,
                'credit' => 0,
                'description' => "Penerimaan {$acceptedQty} {$item->product->uom_purchase} {$item->product->name}",
            ];
        }

        if (empty($postings)) {
            \Log::warning("GoodReceipt {$this->receipt_number} tidak menghasilkan jurnal karena tidak ada inventory account!");
            return;
        }

        // Kredit: Hutang Usaha (Account Payable)
        // Cari akun "Hutang Usaha" di Chart of Accounts
        $accountPayableAccount = \App\Models\ChartOfAccount::where('code', 'like', '2-1%')
            ->where('name', 'like', '%Hutang Usaha%')
            ->first();

        if (!$accountPayableAccount) {
            \Log::error("Akun Hutang Usaha tidak ditemukan! Buat akun dengan code 2-1xxx terlebih dahulu.");
            return;
        }

        $postings[] = [
            'account_id' => $accountPayableAccount->id,
            'debit' => 0,
            'credit' => $totalValue,
            'description' => "Hutang ke {$this->purchaseOrder->vendor->name}",
        ];

        // Buat journal entry
        try {
            $journalService->createJournalEntry([
                'posting_date' => $this->receipt_date,
                'description' => "Good Receipt: {$this->receipt_number} dari PO {$this->purchaseOrder->po_number}",
                'referenceable' => $this,
                'postings' => $postings,
            ], autoPost: true); // Langsung post (tidak draft)

            \Log::info("Journal entry created for GR {$this->receipt_number}, Total: Rp " . number_format($totalValue, 2));
        } catch (\Exception $e) {
            \Log::error("Failed to create journal for GR {$this->receipt_number}: " . $e->getMessage());
            throw $e;
        }
    }
}
