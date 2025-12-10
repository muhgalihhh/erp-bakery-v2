<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    // Movement Type Constants
    const TYPE_IN = 'IN';
    const TYPE_OUT = 'OUT';
    const TYPE_ADJUSTMENT = 'ADJUSTMENT';

    protected $fillable = [
        'type',
        'reference_type',
        'reference_id',
        'reference_number',
        'product_id',
        'quantity',
        'uom',
        'balance_after',
        'warehouse_code',
        'batch_number',
        'expired_date',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'balance_after' => 'decimal:4',
        'expired_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method to auto-fill user tracking
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    /**
     * Relationships
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Helper Methods
     */
    public function isInbound(): bool
    {
        return $this->type === self::TYPE_IN;
    }

    public function isOutbound(): bool
    {
        return $this->type === self::TYPE_OUT;
    }

    public function isAdjustment(): bool
    {
        return $this->type === self::TYPE_ADJUSTMENT;
    }

    /**
     * Scope for filtering
     */
    public function scopeInbound($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    public function scopeOutbound($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', self::TYPE_ADJUSTMENT);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
