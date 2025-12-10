<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ChartOfAccount;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Chart of Accounts for linking (SIMPLIFIED VERSION)
        $persediaanBahanBaku = ChartOfAccount::where('code', '1-1400')->first();
        $persediaanBarangJadi = ChartOfAccount::where('code', '1-1500')->first();
        $hpp = ChartOfAccount::where('code', '5-1000')->first();
        $penjualan = ChartOfAccount::where('code', '4-1000')->first();

        // ============================================
        // RAW MATERIALS (Bahan Baku)
        // ============================================

        Product::create([
            'sku' => 'TP-SEGITIGA-001',
            'name' => 'Tepung Terigu Segitiga Biru 1Kg',
            'description' => 'Tepung terigu premium untuk roti dan kue',
            'type' => 'raw',
            'is_purchasable' => true,
            'is_sellable' => false,
            'uom_purchase' => 'Sak',
            'uom_stock' => 'Kg',
            'uom_usage' => 'Gram',
            'conversion_purchase_to_stock' => 25, // 1 Sak = 25 Kg
            'conversion_stock_to_usage' => 1000, // 1 Kg = 1000 Gram
            'purchase_price' => 500000, // Rp 500,000 per Sak
            'selling_price' => 0,
            'standard_cost' => 20000, // Rp 20,000 per Kg
            'current_stock' => 100, // 100 Kg
            'minimum_stock' => 25,
            'maximum_stock' => 200,
            'inventory_account_id' => $persediaanBahanBaku->id,
            'expense_account_id' => $hpp->id,
            'is_active' => true,
            'barcode' => '8991234567890',
        ]);

        Product::create([
            'sku' => 'TELUR-001',
            'name' => 'Telur Ayam Grade A',
            'description' => 'Telur ayam segar grade A untuk pembuatan kue',
            'type' => 'raw',
            'is_purchasable' => true,
            'is_sellable' => false,
            'uom_purchase' => 'Kg',
            'uom_stock' => 'Kg',
            'uom_usage' => 'Gram',
            'conversion_purchase_to_stock' => 1,
            'conversion_stock_to_usage' => 1000,
            'purchase_price' => 28000, // Rp 28,000 per Kg
            'selling_price' => 0,
            'standard_cost' => 28000,
            'current_stock' => 50,
            'minimum_stock' => 10,
            'maximum_stock' => 100,
            'inventory_account_id' => $persediaanBahanBaku->id,
            'expense_account_id' => $hpp->id,
            'is_active' => true,
        ]);

        Product::create([
            'sku' => 'MENTEGA-001',
            'name' => 'Mentega Anchor 1Kg',
            'description' => 'Mentega tawar berkualitas tinggi',
            'type' => 'raw',
            'is_purchasable' => true,
            'is_sellable' => false,
            'uom_purchase' => 'Kg',
            'uom_stock' => 'Kg',
            'uom_usage' => 'Gram',
            'conversion_purchase_to_stock' => 1,
            'conversion_stock_to_usage' => 1000,
            'purchase_price' => 85000, // Rp 85,000 per Kg
            'selling_price' => 0,
            'standard_cost' => 85000,
            'current_stock' => 20,
            'minimum_stock' => 5,
            'maximum_stock' => 50,
            'inventory_account_id' => $persediaanBahanBaku->id,
            'expense_account_id' => $hpp->id,
            'is_active' => true,
        ]);

        Product::create([
            'sku' => 'GULA-001',
            'name' => 'Gula Pasir Premium',
            'description' => 'Gula pasir halus untuk kue dan roti',
            'type' => 'raw',
            'is_purchasable' => true,
            'is_sellable' => false,
            'uom_purchase' => 'Karton',
            'uom_stock' => 'Kg',
            'uom_usage' => 'Gram',
            'conversion_purchase_to_stock' => 50, // 1 Karton = 50 Kg
            'conversion_stock_to_usage' => 1000,
            'purchase_price' => 700000, // Rp 700,000 per Karton
            'selling_price' => 0,
            'standard_cost' => 14000, // Rp 14,000 per Kg
            'current_stock' => 75,
            'minimum_stock' => 25,
            'maximum_stock' => 150,
            'inventory_account_id' => $persediaanBahanBaku->id,
            'expense_account_id' => $hpp->id,
            'is_active' => true,
        ]);

        Product::create([
            'sku' => 'RAGI-001',
            'name' => 'Ragi Instan Fermipan 500g',
            'description' => 'Ragi kering instan untuk roti',
            'type' => 'raw',
            'is_purchasable' => true,
            'is_sellable' => false,
            'uom_purchase' => 'Pack',
            'uom_stock' => 'Gram',
            'uom_usage' => 'Gram',
            'conversion_purchase_to_stock' => 500, // 1 Pack = 500 Gram
            'conversion_stock_to_usage' => 1,
            'purchase_price' => 45000, // Rp 45,000 per Pack
            'selling_price' => 0,
            'standard_cost' => 90, // Rp 90 per Gram
            'current_stock' => 2000, // 2000 Gram
            'minimum_stock' => 500,
            'maximum_stock' => 5000,
            'inventory_account_id' => $persediaanBahanBaku->id,
            'expense_account_id' => $hpp->id,
            'is_active' => true,
        ]);

        // ============================================
        // FINISHED GOODS (Barang Jadi)
        // ============================================

        Product::create([
            'sku' => 'RT-TAWAR-001',
            'name' => 'Roti Tawar Jumbo',
            'description' => 'Roti tawar jumbo lembut dan empuk',
            'type' => 'finished',
            'is_purchasable' => false,
            'is_sellable' => true,
            'uom_purchase' => 'Pcs',
            'uom_stock' => 'Pcs',
            'uom_usage' => 'Pcs',
            'conversion_purchase_to_stock' => 1,
            'conversion_stock_to_usage' => 1,
            'purchase_price' => 0,
            'selling_price' => 18000, // Rp 18,000 per Pcs
            'standard_cost' => 12000, // HPP dari BOM nanti
            'current_stock' => 50,
            'minimum_stock' => 20,
            'maximum_stock' => 100,
            'inventory_account_id' => $persediaanBarangJadi->id,
            'expense_account_id' => $hpp->id,
            'income_account_id' => $penjualan->id,
            'is_active' => true,
            'barcode' => '8999123456001',
        ]);

        Product::create([
            'sku' => 'CROISSANT-001',
            'name' => 'Croissant Original',
            'description' => 'Croissant butter berlapis renyah',
            'type' => 'finished',
            'is_purchasable' => false,
            'is_sellable' => true,
            'uom_purchase' => 'Pcs',
            'uom_stock' => 'Pcs',
            'uom_usage' => 'Pcs',
            'conversion_purchase_to_stock' => 1,
            'conversion_stock_to_usage' => 1,
            'purchase_price' => 0,
            'selling_price' => 12000, // Rp 12,000 per Pcs
            'standard_cost' => 8000, // HPP dari BOM
            'current_stock' => 30,
            'minimum_stock' => 15,
            'maximum_stock' => 80,
            'inventory_account_id' => $persediaanBarangJadi->id,
            'expense_account_id' => $hpp->id,
            'income_account_id' => $penjualan->id,
            'is_active' => true,
            'barcode' => '8999123456002',
        ]);

        Product::create([
            'sku' => 'KUE-NASTAR-001',
            'name' => 'Kue Nastar Premium',
            'description' => 'Kue nastar dengan selai nanas asli (toples isi 30)',
            'type' => 'finished',
            'is_purchasable' => false,
            'is_sellable' => true,
            'uom_purchase' => 'Toples',
            'uom_stock' => 'Toples',
            'uom_usage' => 'Toples',
            'conversion_purchase_to_stock' => 1,
            'conversion_stock_to_usage' => 1,
            'purchase_price' => 0,
            'selling_price' => 75000, // Rp 75,000 per Toples
            'standard_cost' => 50000, // HPP dari BOM
            'current_stock' => 20,
            'minimum_stock' => 10,
            'maximum_stock' => 50,
            'inventory_account_id' => $persediaanBarangJadi->id,
            'expense_account_id' => $hpp->id,
            'income_account_id' => $penjualan->id,
            'is_active' => true,
        ]);

        $this->command->info('✅ Sample products seeded successfully!');
        $this->command->info('   - 5 Raw Materials: Tepung, Telur, Mentega, Gula, Ragi');
        $this->command->info('   - 3 Finished Goods: Roti Tawar, Croissant, Kue Nastar');
    }
}

