<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'subtotal',
        'total',
        'notes',
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
    ];

    /**
     * Get the purchase order
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get all good receipt items for this PO item
     */
    public function goodReceiptItems(): HasMany
    {
        return $this->hasMany(GoodReceiptItem::class);
    }

    /**
     * Calculate item totals automatically
     */
    protected static function booted()
    {
        static::saving(function ($item) {
            // Calculate subtotal
            $item->subtotal = $item->quantity * $item->unit_price;

            // Calculate discount
            if ($item->discount_percentage > 0) {
                $item->discount_amount = $item->subtotal * ($item->discount_percentage / 100);
            }

            // Calculate tax
            $afterDiscount = $item->subtotal - $item->discount_amount;
            if ($item->tax_percentage > 0) {
                $item->tax_amount = $afterDiscount * ($item->tax_percentage / 100);
            }

            // Calculate total
            $item->total = $afterDiscount + $item->tax_amount;
        });

        static::saved(function ($item) {
            // Recalculate PO totals
            $item->purchaseOrder->calculateTotals();
        });
    }
}
