<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'minimum_spend',
        'point_multiplier',
        'discount_percentage',
        'benefits',
        'priority',
        'color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'minimum_spend' => 'decimal:2',
        'point_multiplier' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'benefits' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get all customers in this tier
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get active customers count
     */
    public function getActiveCustomersCountAttribute(): int
    {
        return $this->customers()->where('is_active', true)->count();
    }

    /**
     * Scope untuk tier yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk sort by priority
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get tier yang sesuai berdasarkan total spending
     */
    public static function getTierForSpending(float $totalSpent): ?self
    {
        return self::active()
            ->where('minimum_spend', '<=', $totalSpent)
            ->orderBy('minimum_spend', 'desc')
            ->first();
    }

    /**
     * Get badge color untuk UI
     */
    public function getBadgeColorAttribute(): string
    {
        return $this->color ?? match ($this->code) {
            'bronze' => 'warning',
            'silver' => 'gray',
            'gold' => 'warning',
            'platinum' => 'info',
            'vip' => 'success',
            default => 'primary',
        };
    }
}
