<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class StockAdjustment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    // Type Constants
    const TYPE_STOCK_OPNAME = 'STOCK_OPNAME';
    const TYPE_DAMAGED = 'DAMAGED';
    const TYPE_EXPIRED = 'EXPIRED';
    const TYPE_OTHER = 'OTHER';

    // Reason Constants
    const REASON_STOCK_COUNT = 'STOCK_COUNT';
    const REASON_DAMAGED = 'DAMAGED';
    const REASON_EXPIRED = 'EXPIRED';
    const REASON_LOST = 'LOST';
    const REASON_FOUND = 'FOUND';
    const REASON_OTHER = 'OTHER';

    // Status Constants
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_CANCELLED = 'CANCELLED';

    protected $fillable = [
        'adjustment_number',
        'adjustment_date',
        'product_id',
        'type',
        'system_quantity',
        'actual_quantity',
        'difference_quantity',
        'uom',
        'reason',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'deleted_by',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'system_quantity' => 'decimal:4',
        'actual_quantity' => 'decimal:4',
        'difference_quantity' => 'decimal:4',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method for auto user tracking
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Status Check Methods
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Approve Adjustment - Update stock and create movement
     */
    public function approve(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            throw new \Exception("Hanya adjustment dengan status DRAFT yang bisa di-approve");
        }

        DB::transaction(function () {
            // Update product stock based on difference
            if ($this->difference_quantity > 0) {
                // Actual > System = Stock bertambah
                $this->product->increaseStock($this->difference_quantity, 'stock');
            } elseif ($this->difference_quantity < 0) {
                // Actual < System = Stock berkurang
                // Prevent negative stock before decreasing
                $qtyToDecrease = abs((float) $this->difference_quantity);
                if (!$this->product->hasEnoughStock($qtyToDecrease)) {
                    throw new \RuntimeException(
                        "Stok {$this->product->name} tidak cukup untuk adjustment. Tersedia: " .
                        number_format((float) $this->product->current_stock, 2) . " {$this->product->uom_stock}, dibutuhkan: " .
                        number_format((float) $qtyToDecrease, 2) . " {$this->product->uom_stock}"
                    );
                }
                $this->product->decreaseStock($qtyToDecrease, 'stock');
            }

            // Create Stock Movement for audit trail
            if ($this->difference_quantity != 0) {
                StockMovement::create([
                    'type' => StockMovement::TYPE_ADJUSTMENT,
                    'reference_type' => self::class,
                    'reference_id' => $this->id,
                    'reference_number' => $this->adjustment_number,
                    'product_id' => $this->product_id,
                    'quantity' => $this->difference_quantity, // Bisa positif atau negatif
                    'uom' => $this->uom,
                    'balance_after' => $this->product->fresh()->current_stock,
                    'notes' => "Adjustment: {$this->getReasonLabel()} - {$this->notes}",
                ]);
            }

            // Update adjustment status
            $this->status = self::STATUS_APPROVED;
            $this->approved_by = auth()->id();
            $this->approved_at = now();
            $this->save();
        });
    }

    /**
     * Cancel Adjustment
     */
    public function cancel(): void
    {
        if ($this->status === self::STATUS_CANCELLED) {
            throw new \Exception("Adjustment sudah dibatalkan");
        }

        if ($this->status === self::STATUS_APPROVED) {
            throw new \Exception("Adjustment yang sudah di-approve tidak bisa dibatalkan");
        }

        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }

    /**
     * Helper: Get reason label in Indonesian
     */
    public function getReasonLabel(): string
    {
        return match ($this->reason) {
            self::REASON_STOCK_COUNT => 'Stock Opname',
            self::REASON_DAMAGED => 'Rusak',
            self::REASON_EXPIRED => 'Kadaluarsa',
            self::REASON_LOST => 'Hilang',
            self::REASON_FOUND => 'Ketemu',
            self::REASON_OTHER => 'Lainnya',
            default => $this->reason,
        };
    }

    /**
     * Scopes
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
