<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'quantity',
        'uom',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'subtotal',
        'total',
        'cost_price',
        'profit',
        'notes',
        'is_custom',
        'fulfillment_status',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'profit' => 'decimal:2',
        'is_custom' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Boot method - auto calculate on saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Auto-calculate subtotal
            $item->subtotal = $item->quantity * $item->unit_price;

            // Apply discount
            if ($item->discount_percentage > 0) {
                $item->discount_amount = $item->subtotal * ($item->discount_percentage / 100);
            }

            // Calculate after discount
            $afterDiscount = $item->subtotal - $item->discount_amount;

            // Apply tax
            if ($item->tax_percentage > 0) {
                $item->tax_amount = $afterDiscount * ($item->tax_percentage / 100);
            }

            // Final total
            $item->total = $afterDiscount + $item->tax_amount;

            // Calculate profit if cost_price available
            if ($item->cost_price) {
                $totalCost = $item->quantity * $item->cost_price;
                $item->profit = $item->total - $totalCost;
            }
        });
    }

    /**
     * Get profit margin for this item
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->total == 0)
            return 0;
        return ($this->profit / $this->total) * 100;
    }
}
