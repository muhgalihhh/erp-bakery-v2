<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomItem extends Model
{
    protected $fillable = [
        'bom_header_id',
        'product_id',
        'quantity',
        'waste_percentage',
        'sequence',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'waste_percentage' => 'decimal:2',
        'quantity_with_waste' => 'decimal:4',
    ];

    /**
     * Relationship: BOM Header
     */
    public function bomHeader(): BelongsTo
    {
        return $this->belongsTo(BomHeader::class, 'bom_header_id');
    }

    /**
     * Relationship: Produk (bahan baku)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Accessor: Quantity with waste (sudah ada di database sebagai computed column)
     * Tapi kita tambahin juga di PHP untuk flexibility
     */
    public function getQuantityWithWasteAttribute(): float
    {
        if (isset($this->attributes['quantity_with_waste'])) {
            return $this->attributes['quantity_with_waste'];
        }

        return $this->quantity * (1 + $this->waste_percentage / 100);
    }
}
