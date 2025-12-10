<?php

namespace Database\Seeders;

use App\Models\DiscountRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DiscountRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'Grand Opening - 20% Off',
                'description' => 'Diskon 20% untuk semua pembelian di atas Rp 200,000',
                'coupon_code' => 'GRAND20',
                'start_date' => Carbon::now()->startOfMonth(),
                'end_date' => Carbon::now()->addMonths(2)->endOfMonth(),
                'is_active' => true,
                'priority' => 1,
                'conditions' => [
                    'min_subtotal' => 200000,
                ],
                'actions' => [
                    'discount_type' => 'percentage',
                    'discount_value' => 20,
                    'apply_to' => 'order',
                ],
                'usage_limit' => 1000,
                'usage_limit_per_customer' => 1,
                'usage_count' => 0,
            ],
            [
                'name' => 'Buy 2 Get 1 Free - Croissant',
                'description' => 'Beli 2 Croissant gratis 1',
                'coupon_code' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'priority' => 2,
                'conditions' => [
                    'required_products' => [], // Will be filled with actual product IDs
                    'min_quantity' => 2,
                ],
                'actions' => [
                    'discount_type' => 'free_item',
                    'free_quantity' => 1,
                    'apply_to' => 'cheapest',
                ],
                'usage_limit' => null,
                'usage_limit_per_customer' => null,
                'usage_count' => 0,
            ],
            [
                'name' => 'Birthday Special - 15% Off',
                'description' => 'Diskon 15% untuk pelanggan yang berulang tahun',
                'coupon_code' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addYear(),
                'is_active' => true,
                'priority' => 3,
                'conditions' => [
                    'is_birthday' => true,
                    'min_subtotal' => 50000,
                ],
                'actions' => [
                    'discount_type' => 'percentage',
                    'discount_value' => 15,
                    'apply_to' => 'order',
                ],
                'usage_limit' => null,
                'usage_limit_per_customer' => 1,
                'usage_count' => 0,
            ],
            [
                'name' => 'Weekend Special - 10% Off',
                'description' => 'Diskon 10% untuk pembelian di akhir pekan',
                'coupon_code' => 'WEEKEND10',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(6),
                'is_active' => true,
                'priority' => 4,
                'conditions' => [
                    'day_of_week' => [6, 0], // Saturday, Sunday
                    'min_subtotal' => 100000,
                ],
                'actions' => [
                    'discount_type' => 'percentage',
                    'discount_value' => 10,
                    'apply_to' => 'order',
                ],
                'usage_limit' => null,
                'usage_limit_per_customer' => null,
                'usage_count' => 0,
            ],
            [
                'name' => 'First Purchase - Rp 50,000 Off',
                'description' => 'Diskon Rp 50,000 untuk pembelian pertama di atas Rp 300,000',
                'coupon_code' => 'FIRST50K',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(12),
                'is_active' => true,
                'priority' => 5,
                'conditions' => [
                    'is_first_purchase' => true,
                    'min_subtotal' => 300000,
                ],
                'actions' => [
                    'discount_type' => 'fixed',
                    'discount_value' => 50000,
                    'apply_to' => 'order',
                ],
                'usage_limit' => null,
                'usage_limit_per_customer' => 1,
                'usage_count' => 0,
            ],
            [
                'name' => 'Gold Member Exclusive - 5% Extra',
                'description' => 'Diskon tambahan 5% untuk member Gold ke atas',
                'coupon_code' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addYear(),
                'is_active' => true,
                'priority' => 6,
                'conditions' => [
                    'customer_tiers' => ['GOLD', 'PLATINUM', 'VIP'],
                    'min_subtotal' => 150000,
                ],
                'actions' => [
                    'discount_type' => 'percentage',
                    'discount_value' => 5,
                    'apply_to' => 'order',
                ],
                'usage_limit' => null,
                'usage_limit_per_customer' => null,
                'usage_count' => 0,
            ],
            [
                'name' => 'Morning Rush - Free Coffee',
                'description' => 'Kopi gratis untuk pembelian di jam 7-9 pagi',
                'coupon_code' => null,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(3),
                'is_active' => true,
                'priority' => 7,
                'conditions' => [
                    'time_range' => ['07:00', '09:00'],
                    'min_subtotal' => 80000,
                ],
                'actions' => [
                    'discount_type' => 'free_item',
                    'free_item' => 'coffee', // Will be replaced with actual product ID
                    'free_quantity' => 1,
                ],
                'usage_limit' => null,
                'usage_limit_per_customer' => 1,
                'usage_count' => 0,
            ],
        ];

        foreach ($discounts as $discount) {
            DiscountRule::create($discount);
        }
    }
}
