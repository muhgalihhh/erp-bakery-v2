<?php

namespace Database\Seeders;

use App\Models\CustomerTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'name' => 'Bronze',
                'code' => 'BRONZE',
                'minimum_spend' => 0,
                'point_multiplier' => 1.0,
                'discount_percentage' => 0,
                'benefits' => [
                    'Earn 1 point per Rp 1,000',
                    'Access to regular promotions',
                    'Birthday greeting',
                ],
                'priority' => 1,
                'color' => '#CD7F32',
                'icon' => 'heroicon-o-star',
                'is_active' => true,
            ],
            [
                'name' => 'Silver',
                'code' => 'SILVER',
                'minimum_spend' => 1000000, // 1 Juta
                'point_multiplier' => 1.2,
                'discount_percentage' => 5,
                'benefits' => [
                    'Earn 1.2 points per Rp 1,000',
                    '5% discount on all purchases',
                    'Priority customer service',
                    'Birthday discount 10%',
                    'Early access to new products',
                ],
                'priority' => 2,
                'color' => '#C0C0C0',
                'icon' => 'heroicon-o-star',
                'is_active' => true,
            ],
            [
                'name' => 'Gold',
                'code' => 'GOLD',
                'minimum_spend' => 5000000, // 5 Juta
                'point_multiplier' => 1.5,
                'discount_percentage' => 10,
                'benefits' => [
                    'Earn 1.5 points per Rp 1,000',
                    '10% discount on all purchases',
                    'Free delivery for orders above Rp 100,000',
                    'Birthday discount 15%',
                    'Exclusive member events',
                    'Complimentary gift wrapping',
                ],
                'priority' => 3,
                'color' => '#FFD700',
                'icon' => 'heroicon-o-star',
                'is_active' => true,
            ],
            [
                'name' => 'Platinum',
                'code' => 'PLATINUM',
                'minimum_spend' => 20000000, // 20 Juta
                'point_multiplier' => 2.0,
                'discount_percentage' => 15,
                'benefits' => [
                    'Earn 2 points per Rp 1,000',
                    '15% discount on all purchases',
                    'Free delivery (no minimum)',
                    'Birthday discount 20%',
                    'VIP event invitations',
                    'Personal shopping assistant',
                    'Custom cake design service',
                    'Points never expire',
                ],
                'priority' => 4,
                'color' => '#E5E4E2',
                'icon' => 'heroicon-o-trophy',
                'is_active' => true,
            ],
            [
                'name' => 'VIP',
                'code' => 'VIP',
                'minimum_spend' => 50000000, // 50 Juta
                'point_multiplier' => 3.0,
                'discount_percentage' => 20,
                'benefits' => [
                    'Earn 3 points per Rp 1,000',
                    '20% discount on all purchases',
                    'Free delivery (no minimum)',
                    'Birthday discount 25%',
                    'Exclusive VIP lounge access',
                    'Dedicated account manager',
                    'Private tasting sessions',
                    'Complimentary catering consultation',
                    'Priority production for custom orders',
                    'Lifetime points (never expire)',
                    'Annual gift basket',
                ],
                'priority' => 5,
                'color' => '#8B008B',
                'icon' => 'heroicon-o-sparkles',
                'is_active' => true,
            ],
        ];

        foreach ($tiers as $tier) {
            CustomerTier::create($tier);
        }
    }
}
