<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BomHeader extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'bom_code',
        'version',
        'description',
        'quantity_produced',
        'is_active',
        'is_default',
        'production_time_minutes',
        'instructions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'quantity_produced' => 'decimal:4',
    ];

    /**
     * Relationship: Produk yang dihasilkan
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship: Bahan-bahan dalam resep
     */
    public function items(): HasMany
    {
        return $this->hasMany(BomItem::class, 'bom_header_id')->orderBy('sequence');
    }

    /**
     * Relationship: Creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Method: Hitung total biaya bahan baku (HPP)
     */
    public function calculateCost(): float
    {
        $totalCost = 0;

        foreach ($this->items as $item) {
            // Ambil harga bahan baku
            $materialCost = $item->product->purchase_price ?? 0;

            // Quantity termasuk waste
            $quantityNeeded = $item->quantity_with_waste;

            // Total biaya = harga * quantity
            $totalCost += $materialCost * $quantityNeeded;
        }

        // Bagi dengan quantity produced untuk dapat cost per unit
        return $this->quantity_produced > 0
            ? $totalCost / $this->quantity_produced
            : $totalCost;
    }

    /**
     * Boot method: Auto-generate BOM code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bom) {
            if (empty($bom->bom_code)) {
                $bom->bom_code = static::generateBomCode();
            }
        });
    }

    /**
     * Generate BOM code otomatis
     * Format: BOM-2025-001
     */
    public static function generateBomCode(): string
    {
        $year = date('Y');
        $prefix = "BOM-{$year}-";

        $lastBom = static::where('bom_code', 'like', "{$prefix}%")
            ->orderBy('bom_code', 'desc')
            ->first();

        if ($lastBom) {
            $lastNumber = (int) substr($lastBom->bom_code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
