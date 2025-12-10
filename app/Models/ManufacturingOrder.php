<?php

namespace App\Models;

use App\Services\JournalService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ManufacturingOrder extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'mo_number',
        'production_date',
        'planned_start_date',
        'planned_finish_date',
        'actual_start_time',
        'actual_finish_time',
        'product_id',
        'bom_header_id',
        'quantity_to_produce',
        'quantity_produced',
        'quantity_scrapped',
        'status',
        'material_cost',
        'labor_cost',
        'overhead_cost',
        'cost_per_unit',
        'work_center',
        'supervisor_id',
        'notes',
        'completion_notes',
        'created_by',
        'confirmed_by',
        'completed_by',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'production_date' => 'date',
        'planned_start_date' => 'date',
        'planned_finish_date' => 'date',
        'actual_start_time' => 'datetime',
        'actual_finish_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'quantity_to_produce' => 'decimal:4',
        'quantity_produced' => 'decimal:4',
        'quantity_scrapped' => 'decimal:4',
        'material_cost' => 'decimal:4',
        'labor_cost' => 'decimal:4',
        'overhead_cost' => 'decimal:4',
        'cost_per_unit' => 'decimal:4',
    ];

    /**
     * Relationships
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function bomHeader(): BelongsTo
    {
        return $this->belongsTo(BomHeader::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ManufacturingOrderMaterial::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Boot method untuk auto-generate MO number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mo) {
            if (empty($mo->mo_number)) {
                $mo->mo_number = self::generateMONumber();
            }
            if (empty($mo->created_by)) {
                $mo->created_by = auth()->id();
            }
        });
    }

    /**
     * Generate MO Number: MO-YYYYMM-XXXX
     */
    public static function generateMONumber(): string
    {
        $prefix = 'MO-';
        $yearMonth = now()->format('Ym');

        $lastMO = self::withTrashed()
            ->where('mo_number', 'like', $prefix . $yearMonth . '%')
            ->orderBy('mo_number', 'desc')
            ->first();

        if ($lastMO) {
            $lastNumber = (int) substr($lastMO->mo_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $yearMonth . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Confirm MO - prepare materials from BOM
     */
    public function confirm(): void
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return;
        }

        DB::transaction(function () {
            // Load BOM items dan create material requirements
            $bomItems = $this->bomHeader->items;

            foreach ($bomItems as $bomItem) {
                // Calculate planned quantity based on BOM ratio
                $multiplier = $this->quantity_to_produce / $this->bomHeader->quantity_produced;
                $plannedQty = $bomItem->quantity_with_waste * $multiplier;

                // Get current cost dari inventory (average cost)
                $unitCost = $bomItem->product->averageCost ?? 0;

                $this->materials()->create([
                    'product_id' => $bomItem->product_id,
                    'planned_quantity' => $plannedQty,
                    'actual_quantity' => $plannedQty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $plannedQty * $unitCost,
                    'is_consumed' => false,
                ]);
            }

            // Update status
            $this->status = self::STATUS_CONFIRMED;
            $this->confirmed_by = auth()->id();
            $this->confirmed_at = now();
            $this->save();

            \Log::info("Manufacturing Order {$this->mo_number} confirmed with " . $bomItems->count() . " materials");
        });
    }

    /**
     * Start Production
     */
    public function start(): void
    {
        if ($this->status !== self::STATUS_CONFIRMED) {
            throw new \Exception("Can only start confirmed MO");
        }

        $this->status = self::STATUS_IN_PROGRESS;
        $this->actual_start_time = now();
        $this->save();
    }

    /**
     * Complete Production - consume materials, produce finished goods, auto-post journals
     */
    public function complete(): void
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            throw new \Exception("Can only complete in-progress MO");
        }

        DB::transaction(function () {
            // 1. Consume materials
            $totalMaterialCost = 0;

            foreach ($this->materials as $material) {
                if (!$material->is_consumed) {
                    $material->product->decreaseStock($material->actual_quantity, 'stock');
                    $material->is_consumed = true;
                    $material->consumed_at = now();
                    $material->save();
                    $totalMaterialCost += $material->total_cost;

                    // **TRACK STOCK MOVEMENT OUT (konsumsi bahan baku)**
                    \App\Models\StockMovement::create([
                        'type' => \App\Models\StockMovement::TYPE_OUT,
                        'reference_type' => self::class,
                        'reference_id' => $this->id,
                        'reference_number' => $this->mo_number,
                        'product_id' => $material->product_id,
                        'quantity' => $material->actual_quantity, // Dalam stock UOM
                        'uom' => $material->product->uom_stock,
                        'balance_after' => $material->product->fresh()->current_stock,
                        'notes' => "Konsumsi untuk produksi {$this->product->name}",
                    ]);
                }
            }

            // 2. Increase finished goods
            $this->product->increaseStock($this->quantity_produced, 'stock');

            // **TRACK STOCK MOVEMENT IN (hasil produksi)**
            \App\Models\StockMovement::create([
                'type' => \App\Models\StockMovement::TYPE_IN,
                'reference_type' => self::class,
                'reference_id' => $this->id,
                'reference_number' => $this->mo_number,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity_produced, // Dalam stock UOM
                'uom' => $this->product->uom_stock,
                'balance_after' => $this->product->fresh()->current_stock,
                'notes' => "Hasil produksi dari MO {$this->mo_number}",
            ]);

            // 3. Calculate cost
            $this->material_cost = $totalMaterialCost;
            $totalCost = $this->material_cost + $this->labor_cost + $this->overhead_cost;

            if ($this->quantity_produced > 0) {
                $this->cost_per_unit = $totalCost / $this->quantity_produced;
            }

            $this->status = self::STATUS_COMPLETED;
            $this->actual_finish_time = now();
            $this->completed_by = auth()->id();
            $this->completed_at = now();
            $this->save();

            // 4. POST JOURNAL
            $this->postJournalEntry();
        });
    }

    /**
     * Auto-post Journal Entry
     */
    protected function postJournalEntry(): void
    {
        $journalService = app(JournalService::class);
        $postings = [];

        // CREDIT: Raw Materials (berkurang) - sesuai actual cost materials
        foreach ($this->materials as $material) {
            if ($material->product->inventory_account_id) {
                $postings[] = [
                    'account_id' => $material->product->inventory_account_id,
                    'debit' => 0,
                    'credit' => $material->total_cost,
                    'line_description' => "Konsumsi {$material->actual_quantity} {$material->product->uom_stock} {$material->product->name}",
                ];
            }
        }

        // DEBIT: Finished Goods (bertambah) - HANYA material cost yang di-transfer
        // Labor & Overhead tidak di-jurnal karena sudah dibayar sebelumnya
        if ($this->product->inventory_account_id && !empty($postings)) {
            // Total cost yang ditransfer = hanya material cost
            // Labor & overhead sudah dibayar tunai/accrued, jadi tidak perlu dijurnal lagi
            // HPP total tetap dicatat di MO untuk tracking, tapi journal hanya untuk material transfer

            $postings[] = [
                'account_id' => $this->product->inventory_account_id,
                'debit' => $this->material_cost, // Hanya material cost
                'credit' => 0,
                'line_description' => "Produksi {$this->quantity_produced} {$this->product->uom_stock} {$this->product->name}",
            ];

            $journalService->createJournalEntry([
                'posting_date' => $this->production_date,
                'description' => "Manufacturing Order: {$this->mo_number} - Transfer Material to Finished Goods",
                'referenceable' => $this,
                'postings' => $postings,
            ], autoPost: true);

            \Log::info("Journal posted for MO {$this->mo_number} - Material transfer: Rp " . number_format($this->material_cost, 2));
        }
    }
}

