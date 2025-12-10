<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'email',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'postal_code',
        'customer_tier_id',
        'total_points',
        'total_spent',
        'transaction_count',
        'last_purchase_date',
        'instagram',
        'facebook',
        'preferences',
        'subscribe_newsletter',
        'subscribe_whatsapp',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_purchase_date' => 'date',
        'total_points' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'transaction_count' => 'integer',
        'preferences' => 'array',
        'subscribe_newsletter' => 'boolean',
        'subscribe_whatsapp' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get customer tier
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(CustomerTier::class, 'customer_tier_id');
    }

    /**
     * Get all sales orders
     */
    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    /**
     * Get points history
     */
    public function pointsLedger(): HasMany
    {
        return $this->hasMany(CustomerPointsLedger::class);
    }

    /**
     * Get user who created
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get user who updated
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTier($query, $tierId)
    {
        return $query->where('customer_tier_id', $tierId);
    }

    /**
     * Earn points dari transaksi
     */
    public function earnPoints(float $amount, $reference = null, string $description = null): CustomerPointsLedger
    {
        $multiplier = $this->tier?->point_multiplier ?? 1.0;
        $points = floor($amount / 10000) * $multiplier; // 1 poin per 10rb rupiah

        $this->increment('total_points', $points);

        return $this->pointsLedger()->create([
            'type' => 'earn',
            'points' => $points,
            'balance_after' => $this->fresh()->total_points,
            'referenceable_type' => $reference ? get_class($reference) : null,
            'referenceable_id' => $reference?->id,
            'reference_number' => $reference?->order_number ?? null,
            'expiry_date' => now()->addYear(), // Expire 1 tahun
            'description' => $description ?? "Poin dari pembelian Rp " . number_format($amount, 0, ',', '.'),
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Redeem/use points
     */
    public function redeemPoints(float $points, $reference = null, string $description = null): CustomerPointsLedger
    {
        if ($points > $this->total_points) {
            throw new \Exception("Insufficient points. Available: {$this->total_points}, Required: {$points}");
        }

        $this->decrement('total_points', $points);

        return $this->pointsLedger()->create([
            'type' => 'redeem',
            'points' => -$points,
            'balance_after' => $this->fresh()->total_points,
            'referenceable_type' => $reference ? get_class($reference) : null,
            'referenceable_id' => $reference?->id,
            'reference_number' => $reference?->order_number ?? null,
            'description' => $description ?? "Penukaran {$points} poin",
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Update total spent dan cek tier upgrade
     */
    public function updateTotalSpent(float $amount): void
    {
        $this->increment('total_spent', $amount);
        $this->increment('transaction_count');
        $this->update(['last_purchase_date' => now()]);

        // Auto upgrade tier jika eligible
        $this->checkAndUpgradeTier();
    }

    /**
     * Check dan upgrade tier otomatis
     */
    public function checkAndUpgradeTier(): void
    {
        $newTier = CustomerTier::getTierForSpending($this->total_spent);

        if ($newTier && $newTier->id !== $this->customer_tier_id) {
            $this->update(['customer_tier_id' => $newTier->id]);

            // TODO: Send notification "Selamat! Anda naik ke tier {$newTier->name}"
        }
    }

    /**
     * Check if birthday (untuk diskon ulang tahun)
     */
    public function isBirthday(): bool
    {
        if (!$this->date_of_birth) {
            return false;
        }

        return $this->date_of_birth->format('m-d') === now()->format('m-d');
    }

    /**
     * Get available points (non-expired)
     */
    public function getAvailablePointsAttribute(): float
    {
        return $this->pointsLedger()
            ->where('type', '!=', 'redeem')
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            })
            ->sum('points');
    }
}
