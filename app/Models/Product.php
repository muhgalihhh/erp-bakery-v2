<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'type',
        'is_sellable',
        'is_purchasable',
        'uom_purchase',
        'uom_stock',
        'uom_usage',
        'conversion_purchase_to_stock',
        'conversion_stock_to_usage',
        'purchase_price',
        'selling_price',
        'standard_cost',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'reorder_point',
        'income_account_id',
        'expense_account_id',
        'inventory_account_id',
        'is_active',
        'barcode',
        'image_url',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_sellable' => 'boolean',
        'is_purchasable' => 'boolean',
        'is_active' => 'boolean',
        'conversion_purchase_to_stock' => 'decimal:4',
        'conversion_stock_to_usage' => 'decimal:4',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'standard_cost' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'maximum_stock' => 'decimal:2',
        'reorder_point' => 'decimal:2',
    ];

    /**
     * Relationship: Income Account (Pendapatan)
     */
    public function incomeAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'income_account_id');
    }

    /**
     * Relationship: Expense Account (HPP)
     */
    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'expense_account_id');
    }

    /**
     * Relationship: Inventory Account (Persediaan)
     */
    public function inventoryAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'inventory_account_id');
    }

    /**
     * Relationship: BOM untuk produk ini (sebagai output)
     */
    public function boms(): HasMany
    {
        return $this->hasMany(BomHeader::class, 'product_id');
    }

    /**
     * Relationship: Default/Active BOM
     */
    public function activeBom(): HasMany
    {
        return $this->hasMany(BomHeader::class, 'product_id')->where('is_active', true)->where('is_default', true);
    }

    /**
     * Relationship: Stock Movements untuk produk ini
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id')->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Hanya produk aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Produk yang bisa dijual
     */
    public function scopeSellable($query)
    {
        return $query->where('is_sellable', true);
    }

    /**
     * Scope: Produk yang bisa dibeli
     */
    public function scopePurchasable($query)
    {
        return $query->where('is_purchasable', true);
    }

    /**
     * Scope: Filter berdasarkan tipe
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Method: Cek apakah stok di bawah minimum
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Method: Konversi quantity dari purchase UoM ke stock UoM
     */
    public function convertPurchaseToStock(float $quantity): float
    {
        return $quantity * $this->conversion_purchase_to_stock;
    }

    /**
     * Method: Konversi quantity dari stock UoM ke usage UoM
     */
    public function convertStockToUsage(float $quantity): float
    {
        return $quantity * $this->conversion_stock_to_usage;
    }

    /**
     * Method: Increase stock (from purchase/production)
     * @param float $quantity - quantity in PURCHASE UOM (will be auto-converted to stock UOM)
     * @param string $fromUom - source UOM: 'purchase', 'stock', or 'usage'. Default: 'purchase'
     */
    public function increaseStock(float $quantity, string $fromUom = 'purchase'): void
    {
        $quantityInStockUom = $this->convertToStockUom($quantity, $fromUom);
        $this->current_stock += $quantityInStockUom;
        $this->save();
    }

    /**
     * Method: Decrease stock (from sales/production usage)
     * @param float $quantity - quantity in USAGE UOM (will be auto-converted to stock UOM)
     * @param string $fromUom - source UOM: 'purchase', 'stock', or 'usage'. Default: 'usage'
     */
    public function decreaseStock(float $quantity, string $fromUom = 'usage'): void
    {
        $quantityInStockUom = $this->convertToStockUom($quantity, $fromUom);
        // Prevent negative stock
        if (!$this->hasEnoughStock($quantityInStockUom)) {
            throw new \RuntimeException(
                "Stok {$this->name} tidak cukup. Tersedia: " . number_format((float) $this->current_stock, 2) .
                " {$this->uom_stock}, dibutuhkan: " . number_format((float) $quantityInStockUom, 2) . " {$this->uom_stock}"
            );
        }
        $this->current_stock -= $quantityInStockUom;
        $this->save();
    }

    /**
     * Convert quantity to stock UOM
     * @param float $quantity
     * @param string $fromUom - 'purchase', 'stock', or 'usage'
     * @return float - quantity in stock UOM
     */
    private function convertToStockUom(float $quantity, string $fromUom): float
    {
        return match ($fromUom) {
            'purchase' => $quantity * ($this->conversion_purchase_to_stock ?? 1),
            'usage' => $quantity / ($this->conversion_stock_to_usage ?? 1),
            'stock' => $quantity, // Already in stock UOM
            default => $quantity,
        };
    }

    /**
     * Check if product has enough stock in stock UOM
     */
    public function hasEnoughStock(float $quantityInStockUom): bool
    {
        return $this->current_stock >= $quantityInStockUom;
    }

    /**
     * Method: Check if stock is below reorder point
     */
    public function needsReorder(): bool
    {
        return $this->current_stock <= $this->reorder_point;
    }
}
