<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManufacturingOrderMaterial extends Model
{
    protected $fillable = [
        'manufacturing_order_id',
        'product_id',
        'planned_quantity',
        'actual_quantity',
        'unit_cost',
        'total_cost',
        'is_consumed',
        'consumed_at',
        'notes',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:4',
        'is_consumed' => 'boolean',
        'consumed_at' => 'datetime',
    ];

    public function manufacturingOrder(): BelongsTo
    {
        return $this->belongsTo(ManufacturingOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

