<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SampleCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tiers
        $bronze = CustomerTier::where('code', 'BRONZE')->first();
        $silver = CustomerTier::where('code', 'SILVER')->first();
        $gold = CustomerTier::where('code', 'GOLD')->first();
        $platinum = CustomerTier::where('code', 'PLATINUM')->first();
        $vip = CustomerTier::where('code', 'VIP')->first();

        $customers = [
            // Bronze Tier Customers (New customers)
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'budi.santoso@email.com',
                'customer_tier_id' => $bronze->id,
                'total_points' => 50,
                'total_spent' => 500000,
                'transaction_count' => 3,
                'date_of_birth' => Carbon::parse('1990-05-15'),
                'address' => 'Jl. Merdeka No. 123, Jakarta Pusat',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
                'preferences' => [
                    'favorite_products' => ['Croissant', 'Baguette'],
                    'allergies' => [],
                    'notes' => 'Suka roti yang fresh di pagi hari',
                ],
            ],
            [
                'name' => 'Siti Rahma',
                'phone' => '082345678901',
                'email' => 'siti.rahma@email.com',
                'customer_tier_id' => $bronze->id,
                'total_points' => 75,
                'total_spent' => 750000,
                'transaction_count' => 5,
                'date_of_birth' => Carbon::parse('1995-08-22'),
                'address' => 'Jl. Sudirman No. 456, Jakarta Selatan',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'preferences' => [
                    'favorite_products' => ['Birthday Cake', 'Cupcake'],
                    'allergies' => ['nuts'],
                    'notes' => 'Alergi kacang',
                ],
            ],

            // Silver Tier Customers
            [
                'name' => 'Ahmad Wijaya',
                'phone' => '083456789012',
                'email' => 'ahmad.wijaya@email.com',
                'customer_tier_id' => $silver->id,
                'total_points' => 1500,
                'total_spent' => 1500000,
                'transaction_count' => 12,
                'date_of_birth' => Carbon::parse('1988-03-10'),
                'address' => 'Jl. Gatot Subroto No. 789, Jakarta Selatan',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12930',
                'preferences' => [
                    'favorite_products' => ['Danish Pastry', 'Sourdough Bread'],
                    'allergies' => [],
                    'notes' => 'Regular customer, datang setiap Jumat pagi',
                ],
            ],
            [
                'name' => 'Linda Kusuma',
                'phone' => '084567890123',
                'email' => 'linda.kusuma@email.com',
                'customer_tier_id' => $silver->id,
                'total_points' => 2000,
                'total_spent' => 2000000,
                'transaction_count' => 15,
                'date_of_birth' => Carbon::parse('1992-11-30'),
                'address' => 'Jl. Thamrin No. 321, Jakarta Pusat',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10230',
                'preferences' => [
                    'favorite_products' => ['Wedding Cake', 'Macaron'],
                    'allergies' => [],
                    'notes' => 'Wedding planner, sering pesan untuk klien',
                ],
            ],

            // Gold Tier Customers
            [
                'name' => 'Rina Anggraini',
                'phone' => '085678901234',
                'email' => 'rina.anggraini@email.com',
                'customer_tier_id' => $gold->id,
                'total_points' => 7500,
                'total_spent' => 7500000,
                'transaction_count' => 35,
                'date_of_birth' => Carbon::parse('1985-07-18'),
                'address' => 'Jl. Senopati No. 654, Jakarta Selatan',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12180',
                'preferences' => [
                    'favorite_products' => ['Custom Cake', 'French Bread'],
                    'allergies' => [],
                    'notes' => 'Owner kafe, order weekly untuk stock',
                ],
            ],
            [
                'name' => 'Dedi Prasetyo',
                'phone' => '086789012345',
                'email' => 'dedi.prasetyo@email.com',
                'customer_tier_id' => $gold->id,
                'total_points' => 6000,
                'total_spent' => 6000000,
                'transaction_count' => 28,
                'date_of_birth' => Carbon::parse('1987-12-05'),
                'address' => 'Jl. Kemang Raya No. 987, Jakarta Selatan',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12730',
                'preferences' => [
                    'favorite_products' => ['Artisan Bread', 'Rye Bread'],
                    'allergies' => [],
                    'notes' => 'Pemilik restaurant, regular order',
                ],
            ],

            // Platinum Tier Customer
            [
                'name' => 'Dewi Lestari',
                'phone' => '087890123456',
                'email' => 'dewi.lestari@email.com',
                'customer_tier_id' => $platinum->id,
                'total_points' => 40000,
                'total_spent' => 25000000,
                'transaction_count' => 85,
                'date_of_birth' => Carbon::parse('1980-04-25'),
                'address' => 'Jl. Menteng Raya No. 111, Jakarta Pusat',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10310',
                'preferences' => [
                    'favorite_products' => ['Premium Wedding Cake', 'French Pastry'],
                    'allergies' => [],
                    'notes' => 'Event organizer, frequent large orders',
                ],
            ],

            // VIP Tier Customer
            [
                'name' => 'Hartono Wijaya',
                'phone' => '088901234567',
                'email' => 'hartono.wijaya@email.com',
                'customer_tier_id' => $vip->id,
                'total_points' => 150000,
                'total_spent' => 55000000,
                'transaction_count' => 120,
                'date_of_birth' => Carbon::parse('1975-09-12'),
                'address' => 'Jl. Sudirman Kav. 52-53, Jakarta Selatan',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'preferences' => [
                    'favorite_products' => ['Custom Corporate Cake', 'Artisan Pastry Box'],
                    'allergies' => [],
                    'notes' => 'Corporate client - monthly catering orders',
                ],
            ],

            // Walk-in customer (no tier)
            [
                'name' => 'Customer Walk-In',
                'phone' => '089999999999',
                'email' => null,
                'customer_tier_id' => $bronze->id,
                'total_points' => 0,
                'total_spent' => 0,
                'transaction_count' => 0,
                'date_of_birth' => null,
                'address' => null,
                'city' => null,
                'province' => null,
                'postal_code' => null,
                'preferences' => [
                    'notes' => 'Default walk-in customer account',
                ],
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
