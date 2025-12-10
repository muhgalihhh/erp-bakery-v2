<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_IN_PRODUCTION = 'in_production';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'customer_id',
        'order_date',
        'order_type',
        'order_channel',
        'delivery_date',
        'delivery_time',
        'delivery_address',
        'delivery_phone',
        'delivery_recipient',
        'status',
        'subtotal',
        'discount_amount',
        'discount_source',
        'tax_percentage',
        'tax_amount',
        'shipping_cost',
        'points_used',
        'points_earned',
        'total',
        'payment_status',
        'payment_method',
        'paid_amount',
        'change_amount',
        'payment_details',
        'served_by',
        'prepared_by',
        'customer_notes',
        'internal_notes',
        'created_by',
        'updated_by',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'points_used' => 'decimal:2',
        'points_earned' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_details' => 'array',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function journalEntries(): MorphMany
    {
        return $this->morphMany(JournalEntry::class, 'referenceable');
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'reference');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_DRAFT, self::STATUS_CONFIRMED]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('order_date', today());
    }

    public function scopeByChannel($query, string $channel)
    {
        return $query->where('order_channel', $channel);
    }

    /**
     * Helper methods
     */
    public function canEdit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT]);
    }

    public function canConfirm(): bool
    {
        return $this->status === self::STATUS_DRAFT && $this->items->count() > 0;
    }

    public function canCancel(): bool
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    /**
     * Calculate totals from items
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->discount_amount = $this->items->sum('discount_amount');

        // Tax calculation
        $taxableAmount = $this->subtotal - $this->discount_amount;
        $this->tax_amount = $taxableAmount * ($this->tax_percentage / 100);

        // Final total
        $this->total = $taxableAmount + $this->tax_amount + $this->shipping_cost - $this->points_used;

        $this->save();
    }

    /**
     * Update payment status based on paid amount
     */
    public function updatePaymentStatus(): void
    {
        if ($this->paid_amount >= $this->total) {
            $this->payment_status = self::PAYMENT_PAID;
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = self::PAYMENT_PARTIAL;
        } else {
            $this->payment_status = self::PAYMENT_PENDING;
        }

        $this->save();
    }

    /**
     * Get grand total profit
     */
    public function getTotalProfitAttribute(): float
    {
        return $this->items->sum('profit') ?? 0;
    }

    /**
     * Get profit margin percentage
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->total == 0)
            return 0;
        return ($this->total_profit / $this->total) * 100;
    }
}
