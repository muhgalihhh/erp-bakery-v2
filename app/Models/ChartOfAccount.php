<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'subtype',
        'parent_id',
        'currency',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: Parent Account (untuk hierarki)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    /**
     * Relationship: Child Accounts (sub-accounts)
     */
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    /**
     * Relationship: Journal Postings yang menggunakan akun ini
     */
    public function journalPostings(): HasMany
    {
        return $this->hasMany(JournalPosting::class, 'account_id');
    }

    /**
     * Relationship: User yang membuat
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: User yang terakhir update
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Accessor: Full account name dengan kode
     * Contoh: "1-1310 - Persediaan Tepung"
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }

    /**
     * Scope: Hanya akun aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter berdasarkan tipe
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Method: Hitung balance akun ini
     * (Total Debit - Total Credit untuk Asset/Expense, sebaliknya untuk Liability/Equity/Revenue)
     */
    public function getBalance(): float
    {
        $debits = $this->journalPostings()->sum('debit');
        $credits = $this->journalPostings()->sum('credit');

        // Normal balance: Asset & Expense = Debit, yang lain = Credit
        if (in_array($this->type, ['asset', 'expense'])) {
            return $debits - $credits;
        }

        return $credits - $debits;
    }
}
