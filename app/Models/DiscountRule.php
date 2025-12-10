<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DiscountRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'coupon_code',
        'is_public',
        'start_date',
        'end_date',
        'priority',
        'can_combine',
        'conditions',
        'actions',
        'usage_limit',
        'usage_limit_per_customer',
        'usage_count',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_public' => 'boolean',
        'can_combine' => 'boolean',
        'conditions' => 'array',
        'actions' => 'array',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCoupon($query, string $code)
    {
        return $query->where('coupon_code', $code);
    }

    /**
     * Check if rule is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if customer can use this rule
     */
    public function canBeUsedByCustomer(?Customer $customer = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // If not public, must have coupon code
        if (!$this->is_public && !$this->coupon_code) {
            return false;
        }

        // Check per-customer usage limit
        if ($customer && $this->usage_limit_per_customer) {
            // TODO: Check customer usage count
            // $usageCount = $customer->salesOrders()->where('discount_source', $this->name)->count();
            // if ($usageCount >= $this->usage_limit_per_customer) return false;
        }

        return true;
    }

    /**
     * Evaluate if conditions are met
     */
    public function evaluateConditions(SalesOrder $order, ?Customer $customer = null): bool
    {
        if (!$this->conditions) {
            return true; // No conditions = always true
        }

        $conditions = $this->conditions;

        // Min subtotal
        if (isset($conditions['min_subtotal']) && $order->subtotal < $conditions['min_subtotal']) {
            return false;
        }

        // Max subtotal
        if (isset($conditions['max_subtotal']) && $order->subtotal > $conditions['max_subtotal']) {
            return false;
        }

        // Required products
        if (isset($conditions['required_product_ids'])) {
            $orderProductIds = $order->items->pluck('product_id')->toArray();
            $requiredIds = $conditions['required_product_ids'];

            if (!array_intersect($requiredIds, $orderProductIds)) {
                return false;
            }
        }

        // Customer tier
        if (isset($conditions['customer_tier_codes']) && $customer) {
            $tierCode = $customer->tier?->code;
            if (!in_array($tierCode, $conditions['customer_tier_codes'])) {
                return false;
            }
        }

        // Day of week
        if (isset($conditions['day_of_week'])) {
            $today = now()->format('l'); // Monday, Tuesday, etc
            if (!in_array($today, $conditions['day_of_week'])) {
                return false;
            }
        }

        // Time range
        if (isset($conditions['time_range'])) {
            $now = now()->format('H:i');
            $from = $conditions['time_range']['from'] ?? '00:00';
            $to = $conditions['time_range']['to'] ?? '23:59';

            if ($now < $from || $now > $to) {
                return false;
            }
        }

        // First purchase only
        if (isset($conditions['first_purchase_only']) && $conditions['first_purchase_only'] && $customer) {
            if ($customer->transaction_count > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(SalesOrder $order): float
    {
        if (!$this->actions) {
            return 0;
        }

        $actions = $this->actions;
        $type = $actions['type'] ?? 'percent_off';
        $value = $actions['value'] ?? 0;
        $maxAmount = $actions['max_discount_amount'] ?? null;

        $discountAmount = 0;

        switch ($type) {
            case 'percent_off':
                $discountAmount = $order->subtotal * ($value / 100);
                break;

            case 'amount_off':
                $discountAmount = $value;
                break;

            case 'buy_x_get_y':
                // TODO: Implement buy X get Y logic
                break;

            case 'free_shipping':
                $discountAmount = $order->shipping_cost;
                break;
        }

        // Apply max discount limit
        if ($maxAmount && $discountAmount > $maxAmount) {
            $discountAmount = $maxAmount;
        }

        return $discountAmount;
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
