<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Prioritas: Super Admin dulu untuk testing
        $this->call([
            SuperAdminSeeder::class,
            ChartOfAccountSeeder::class,
            ProductSeeder::class,
            BomSeeder::class,

                // Role Seeders - roles & test users
            HeadBakerSeeder::class,
            InventoryControllerSeeder::class,
            CashierSeeder::class,
            ProductionStaffSeeder::class,

                // Purchasing Module Sample Data
            VendorSeeder::class,
            PurchaseOrderSeeder::class,
        ]);

        // User::factory(10)->create();
    }
}
