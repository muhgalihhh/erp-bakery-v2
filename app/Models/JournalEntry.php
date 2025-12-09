<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'transaction_number',
        'posting_date',
        'description',
        'referenceable_type',
        'referenceable_id',
        'is_posted',
        'posted_at',
        'created_by',
        'posted_by',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'is_posted' => 'boolean',
        'posted_at' => 'datetime',
    ];

    /**
     * Polymorphic relationship ke dokumen sumber
     * (Order, Purchase, ManufacturingOrder, dll)
     */
    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relationship: Detail postings (baris debit/kredit)
     */
    public function postings(): HasMany
    {
        return $this->hasMany(JournalPosting::class, 'journal_entry_id');
    }

    /**
     * Relationship: User yang membuat
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: User yang memposting
     */
    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    /**
     * Scope: Hanya jurnal yang sudah diposting
     */
    public function scopePosted($query)
    {
        return $query->where('is_posted', true);
    }

    /**
     * Scope: Hanya jurnal draft
     */
    public function scopeDraft($query)
    {
        return $query->where('is_posted', false);
    }

    /**
     * Method: Validasi balance (Debit harus = Credit)
     */
    public function isBalanced(): bool
    {
        $totalDebit = $this->postings()->sum('debit');
        $totalCredit = $this->postings()->sum('credit');

        return bccomp($totalDebit, $totalCredit, 4) === 0;
    }

    /**
     * Method: Posting jurnal (lock untuk prevent edit)
     */
    public function post(User $user): bool
    {
        if ($this->is_posted) {
            return false; // Sudah diposting
        }

        if (!$this->isBalanced()) {
            return false; // Tidak balance
        }

        $this->update([
            'is_posted' => true,
            'posted_at' => now(),
            'posted_by' => $user->id,
        ]);

        return true;
    }

    /**
     * Boot method: Auto-generate transaction number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($journalEntry) {
            if (empty($journalEntry->transaction_number)) {
                $journalEntry->transaction_number = static::generateTransactionNumber();
            }
        });
    }

    /**
     * Generate nomor transaksi otomatis
     * Format: JE-2025-12-001
     */
    public static function generateTransactionNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "JE-{$year}-{$month}-";

        $lastEntry = static::where('transaction_number', 'like', "{$prefix}%")
            ->orderBy('transaction_number', 'desc')
            ->first();

        if ($lastEntry) {
            $lastNumber = (int) substr($lastEntry->transaction_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
