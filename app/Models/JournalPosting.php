<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalPosting extends Model
{
    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
        'line_description',
        'tags',
    ];

    protected $casts = [
        'debit' => 'decimal:4',
        'credit' => 'decimal:4',
        'tags' => 'array',
    ];

    /**
     * Relationship: Journal Entry header
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    /**
     * Relationship: Chart of Account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    /**
     * Accessor: Mendapatkan amount (debit atau credit, yang tidak 0)
     */
    public function getAmountAttribute(): float
    {
        return $this->debit > 0 ? $this->debit : $this->credit;
    }

    /**
     * Accessor: Mendapatkan tipe (D atau C)
     */
    public function getTypeAttribute(): string
    {
        return $this->debit > 0 ? 'D' : 'C';
    }
}
