<?php

namespace App\Services;

use App\Models\DiscountRule;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DiscountEngine
{
  /**
   * Find applicable discount rules for given cart data
   *
   * @param array $cartData
   * @return Collection Array of applicable discount rules sorted by priority
   */
  public function findApplicableDiscounts(array $cartData): Collection
  {
    // Get all active discount rules
    $rules = DiscountRule::where('is_active', true)
      ->where(function ($query) {
        $query->whereNull('start_date')
          ->orWhere('start_date', '<=', now());
      })
      ->where(function ($query) {
        $query->whereNull('end_date')
          ->orWhere('end_date', '>=', now());
      })
      ->orderBy('priority', 'asc')
      ->get();

    $applicableRules = [];

    foreach ($rules as $rule) {
      if ($this->isRuleApplicable($rule, $cartData)) {
        $applicableRules[] = $rule;
      }
    }

    return collect($applicableRules);
  }

  /**
   * Get best discount from applicable rules
   *
   * @param array $cartData
   * @return DiscountRule|null
   */
  public function getBestDiscount(array $cartData): ?DiscountRule
  {
    $applicableRules = $this->findApplicableDiscounts($cartData);

    if ($applicableRules->isEmpty()) {
      return null;
    }

    // Return first rule (highest priority)
    return $applicableRules->first();
  }

  /**
   * Calculate discount amount for a rule
   *
   * @param DiscountRule $rule
   * @param array $cartData
   * @return float
   */
  public function calculateDiscountAmount(DiscountRule $rule, array $cartData): float
  {
    $actions = $rule->actions;
    $subtotal = $cartData['subtotal'] ?? 0;

    if (!isset($actions['discount_type'])) {
      return 0;
    }

    switch ($actions['discount_type']) {
      case 'percentage':
        $discountValue = $actions['discount_value'] ?? 0;
        $discountAmount = $subtotal * ($discountValue / 100);

        // Apply max discount limit if set
        if (isset($actions['max_discount_amount']) && $actions['max_discount_amount'] > 0) {
          $discountAmount = min($discountAmount, $actions['max_discount_amount']);
        }

        return $discountAmount;

      case 'fixed':
        return $actions['discount_value'] ?? 0;

      case 'free_item':
        // For free item, discount = price of free item × quantity
        if (isset($actions['free_product_id'])) {
          $freeProduct = \App\Models\Product::find($actions['free_product_id']);
          if ($freeProduct) {
            $freeQty = $actions['free_quantity'] ?? 1;
            return $freeProduct->selling_price * $freeQty;
          }
        }
        return 0;

      default:
        return 0;
    }
  }

  /**
   * Check if a discount rule is applicable to cart
   *
   * @param DiscountRule $rule
   * @param array $cartData
   * @return bool
   */
  protected function isRuleApplicable(DiscountRule $rule, array $cartData): bool
  {
    $conditions = $rule->conditions ?? [];

    if (empty($conditions)) {
      return true; // No conditions = always applicable
    }

    // Check usage limit
    if ($rule->usage_limit > 0 && $rule->usage_count >= $rule->usage_limit) {
      Log::info("Discount {$rule->code}: Usage limit reached");
      return false;
    }

    // Check customer-specific usage limit
    if (isset($cartData['customer_id']) && $rule->usage_per_customer > 0) {
      $customerUsage = \App\Models\SalesOrder::where('customer_id', $cartData['customer_id'])
        ->where('discount_source', $rule->code)
        ->count();

      if ($customerUsage >= $rule->usage_per_customer) {
        Log::info("Discount {$rule->code}: Customer usage limit reached");
        return false;
      }
    }

    // Check minimum subtotal
    if (isset($conditions['min_subtotal'])) {
      if (($cartData['subtotal'] ?? 0) < $conditions['min_subtotal']) {
        Log::info("Discount {$rule->code}: Min subtotal not met");
        return false;
      }
    }

    // Check maximum subtotal
    if (isset($conditions['max_subtotal'])) {
      if (($cartData['subtotal'] ?? 0) > $conditions['max_subtotal']) {
        Log::info("Discount {$rule->code}: Max subtotal exceeded");
        return false;
      }
    }

    // Check minimum quantity
    if (isset($conditions['min_quantity'])) {
      $totalQty = collect($cartData['items'] ?? [])->sum('quantity');
      if ($totalQty < $conditions['min_quantity']) {
        Log::info("Discount {$rule->code}: Min quantity not met");
        return false;
      }
    }

    // Check customer tier
    if (isset($conditions['customer_tier_codes']) && !empty($conditions['customer_tier_codes'])) {
      if (!isset($cartData['customer_id'])) {
        Log::info("Discount {$rule->code}: Requires customer (tier check)");
        return false;
      }

      $customer = Customer::find($cartData['customer_id']);
      // customer_tier_codes stores tier IDs from the form (Select with tier IDs)
      if (!$customer || !$customer->customer_tier_id || !in_array($customer->customer_tier_id, $conditions['customer_tier_codes'])) {
        Log::info("Discount {$rule->code}: Customer tier not eligible");
        return false;
      }
    }

    // Check required products
    if (isset($conditions['required_product_ids']) && !empty($conditions['required_product_ids'])) {
      $cartProductIds = collect($cartData['items'] ?? [])->pluck('product_id')->toArray();

      $hasRequiredProduct = false;
      foreach ($conditions['required_product_ids'] as $requiredProductId) {
        if (in_array($requiredProductId, $cartProductIds)) {
          $hasRequiredProduct = true;
          break;
        }
      }

      if (!$hasRequiredProduct) {
        Log::info("Discount {$rule->code}: Required product not in cart");
        return false;
      }
    }

    // Check day of week
    if (isset($conditions['day_of_week']) && !empty($conditions['day_of_week'])) {
      $today = now()->dayOfWeek; // 0 = Sunday, 6 = Saturday

      if (!in_array($today, $conditions['day_of_week'])) {
        Log::info("Discount {$rule->code}: Not applicable today");
        return false;
      }
    }

    // Check is birthday
    if (isset($conditions['is_birthday']) && $conditions['is_birthday']) {
      if (!isset($cartData['customer_id'])) {
        Log::info("Discount {$rule->code}: Requires customer (birthday check)");
        return false;
      }

      $customer = Customer::find($cartData['customer_id']);
      if (!$customer || !$customer->date_of_birth) {
        Log::info("Discount {$rule->code}: Customer has no birthday");
        return false;
      }

      // Check if today is birthday (ignore year)
      $today = now();
      $birthday = \Carbon\Carbon::parse($customer->date_of_birth);

      if ($today->month != $birthday->month || $today->day != $birthday->day) {
        Log::info("Discount {$rule->code}: Not customer's birthday");
        return false;
      }
    }

    // Check is first purchase
    if (isset($conditions['is_first_purchase']) && $conditions['is_first_purchase']) {
      if (!isset($cartData['customer_id'])) {
        Log::info("Discount {$rule->code}: Requires customer (first purchase check)");
        return false;
      }

      $customer = Customer::find($cartData['customer_id']);
      if (!$customer) {
        return false;
      }

      // Check if customer has any completed orders
      $hasOrders = \App\Models\SalesOrder::where('customer_id', $customer->id)
        ->where('status', \App\Models\SalesOrder::STATUS_COMPLETED)
        ->exists();

      if ($hasOrders) {
        Log::info("Discount {$rule->code}: Not first purchase");
        return false;
      }
    }

    // All conditions passed
    Log::info("Discount {$rule->code}: APPLICABLE!");
    return true;
  }

  /**
   * Apply discount to cart data
   *
   * @param DiscountRule $rule
   * @param array $cartData
   * @return array Modified cart data with discount applied
   */
  public function applyDiscount(DiscountRule $rule, array $cartData): array
  {
    $discountAmount = $this->calculateDiscountAmount($rule, $cartData);

    $cartData['discount_rule_id'] = $rule->id;
    $cartData['discount_source'] = $rule->code;
    $cartData['discount_amount'] = $discountAmount;
    $cartData['discount_name'] = $rule->name;

    // Recalculate totals
    $subtotal = $cartData['subtotal'] ?? 0;
    $taxPercentage = $cartData['tax_percentage'] ?? 11;

    $afterDiscount = $subtotal - $discountAmount;
    $taxAmount = $afterDiscount * ($taxPercentage / 100);
    $total = $afterDiscount + $taxAmount;

    $cartData['tax_amount'] = $taxAmount;
    $cartData['total'] = $total;

    return $cartData;
  }
}
