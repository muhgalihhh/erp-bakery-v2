<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\JournalService;

class VendorPayment extends Model
{
    use SoftDeletes;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment method constants
    const METHOD_CASH = 'cash';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CHECK = 'check';
    const METHOD_GIRO = 'giro';
    const METHOD_OTHER = 'other';

    protected $fillable = [
        'payment_number',
        'vendor_id',
        'purchase_order_id',
        'paid_by',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'bank_account',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: Vendor
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Relationship: Purchase Order (optional reference)
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Relationship: User who made the payment
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Relationship: User who created this record
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: User who last updated this record
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if payment is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if payment is draft
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if payment is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Confirm payment (change status to confirmed)
     */
    public function confirm(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return;
        }

        $this->status = self::STATUS_CONFIRMED;
        $this->save();

        // AUTO-POSTING JOURNAL ENTRY
        // Jurnal: Debit Hutang Usaha (liability turun), Kredit Kas/Bank (asset turun)
        $this->postJournalEntry();
    }

    /**
     * Post journal entry untuk Vendor Payment
     *
     * Logika Akuntansi:
     * - Debit: Hutang Usaha (Liability berkurang)
     * - Kredit: Kas/Bank (Asset berkurang)
     */
    protected function postJournalEntry(): void
    {
        $journalService = app(JournalService::class);

        // Debit: Hutang Usaha
        $accountPayableAccount = \App\Models\ChartOfAccount::where('code', 'like', '2-1%')
            ->where('name', 'like', '%Hutang Usaha%')
            ->first();

        if (!$accountPayableAccount) {
            \Log::error("Akun Hutang Usaha tidak ditemukan! Buat akun dengan code 2-1xxx terlebih dahulu.");
            return;
        }

        // Kredit: Kas atau Bank (tergantung payment method)
        $cashBankAccount = $this->getCashBankAccount();

        if (!$cashBankAccount) {
            \Log::error("Akun Kas/Bank tidak ditemukan untuk payment method: {$this->payment_method}");
            return;
        }

        $postings = [
            // Debit: Hutang Usaha (berkurang)
            [
                'account_id' => $accountPayableAccount->id,
                'debit' => $this->amount,
                'credit' => 0,
                'description' => "Pembayaran hutang ke {$this->vendor->name}",
            ],
            // Kredit: Kas/Bank (berkurang)
            [
                'account_id' => $cashBankAccount->id,
                'debit' => 0,
                'credit' => $this->amount,
                'description' => $this->getPaymentDescription(),
            ],
        ];

        try {
            $journalService->createJournalEntry([
                'posting_date' => $this->payment_date,
                'description' => "Pembayaran Supplier: {$this->payment_number} ke {$this->vendor->name}",
                'referenceable' => $this,
                'postings' => $postings,
            ], autoPost: true);

            \Log::info("Journal entry created for Payment {$this->payment_number}, Amount: Rp " . number_format($this->amount, 2));
        } catch (\Exception $e) {
            \Log::error("Failed to create journal for Payment {$this->payment_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get appropriate cash/bank account based on payment method
     */
    protected function getCashBankAccount(): ?\App\Models\ChartOfAccount
    {
        return match ($this->payment_method) {
            self::METHOD_CASH => \App\Models\ChartOfAccount::where('code', 'like', '1-1%')
                ->where('name', 'like', '%Kas%')
                ->first(),

            self::METHOD_BANK_TRANSFER,
            self::METHOD_CHECK,
            self::METHOD_GIRO => \App\Models\ChartOfAccount::where('code', 'like', '1-1%')
                ->where('name', 'like', '%Bank%')
                ->first(),

            default => null,
        };
    }

    /**
     * Get payment description based on method
     */
    protected function getPaymentDescription(): string
    {
        $desc = match ($this->payment_method) {
            self::METHOD_CASH => "Pembayaran tunai",
            self::METHOD_BANK_TRANSFER => "Transfer bank",
            self::METHOD_CHECK => "Pembayaran cek",
            self::METHOD_GIRO => "Pembayaran giro",
            default => "Pembayaran lainnya",
        };

        if ($this->reference_number) {
            $desc .= " ({$this->reference_number})";
        }

        if ($this->bank_account) {
            $desc .= " via {$this->bank_account}";
        }

        return $desc;
    }

    /**
     * Cancel payment
     */
    public function cancel(): void
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return;
        }

        $this->status = self::STATUS_CANCELLED;
        $this->save();

        // TODO: Reverse journal entry if was confirmed
        // TODO: Update vendor balance
        // TODO: Update PO payment status if linked
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::METHOD_CASH => 'Cash',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_CHECK => 'Check',
            self::METHOD_GIRO => 'Giro',
            self::METHOD_OTHER => 'Other',
            default => $this->payment_method,
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => $this->status,
        };
    }

    /**
     * Get status color for badge
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'warning',
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'gray',
        };
    }
}
