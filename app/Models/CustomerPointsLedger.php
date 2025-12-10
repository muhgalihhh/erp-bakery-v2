<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomerPointsLedger extends Model
{
    use HasFactory;

    protected $table = 'customer_points_ledger';

    // Type constants
    const TYPE_EARN = 'earn';
    const TYPE_REDEEM = 'redeem';
    const TYPE_EXPIRE = 'expire';
    const TYPE_ADJUST = 'adjust';

    protected $fillable = [
        'customer_id',
        'type',
        'points',
        'balance_after',
        'referenceable_type',
        'referenceable_id',
        'reference_number',
        'expiry_date',
        'is_expired',
        'description',
        'created_by',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'expiry_date' => 'date',
        'is_expired' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeEarned($query)
    {
        return $query->where('type', self::TYPE_EARN);
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', self::TYPE_REDEEM);
    }

    public function scopeExpired($query)
    {
        return $query->where('type', self::TYPE_EXPIRE)->orWhere('is_expired', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_expired', false)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            });
    }

    /**
     * Check if earn/redeem (not expire/adjust)
     */
    public function isEarn(): bool
    {
        return $this->type === self::TYPE_EARN;
    }

    public function isRedeem(): bool
    {
        return $this->type === self::TYPE_REDEEM;
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'is_expired' => true,
            'type' => self::TYPE_EXPIRE,
        ]);
    }
}
