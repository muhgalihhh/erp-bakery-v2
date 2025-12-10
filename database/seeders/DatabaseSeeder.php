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
        // Simplified for Small Bakery
        $this->call([
            SuperAdminSeeder::class,           // 1 Super Admin (owner)
            ChartOfAccountSeederSimple::class, // SIMPLIFIED: 17 akun saja (was 32)
            ProductSeeder::class,              // Produk minimal
            BomSeeder::class,                  // Resep minimal
            VendorSeeder::class,               // Supplier utama
                // PurchaseOrderSeeder::class,     // Optional: sample PO (bisa diaktifkan jika perlu)

                // Sales & CRM Module Seeders
            CustomerTierSeeder::class,         // Loyalty tiers (Bronze - VIP)
            DiscountRuleSeeder::class,         // Promo rules
            SampleCustomerSeeder::class,       // Sample customers across all tiers

            // REMOVED: Role seeders untuk toko kecil (HeadBaker, InventoryController, Cashier, ProductionStaff)
            // Untuk toko kecil, cukup Super Admin yang bisa akses semua
        ]);
    }
}
