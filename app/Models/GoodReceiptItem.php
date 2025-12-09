<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'good_receipt_id',
        'purchase_order_item_id',
        'product_id',
        'ordered_quantity',
        'received_quantity',
        'rejected_quantity',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:4',
        'received_quantity' => 'decimal:4',
        'rejected_quantity' => 'decimal:4',
    ];

    /**
     * Get the good receipt
     */
    public function goodReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodReceipt::class);
    }

    /**
     * Get the purchase order item
     */
    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get accepted quantity (received - rejected)
     */
    public function getAcceptedQuantityAttribute(): float
    {
        return $this->received_quantity - $this->rejected_quantity;
    }
}
