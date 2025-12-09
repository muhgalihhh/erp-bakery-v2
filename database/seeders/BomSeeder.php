<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\BomHeader;
use App\Models\BomItem;

class BomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Products
        $rotiTawar = Product::where('sku', 'RT-TAWAR-001')->first();
        $croissant = Product::where('sku', 'CROISSANT-001')->first();
        $tepung = Product::where('sku', 'TP-SEGITIGA-001')->first();
        $telur = Product::where('sku', 'TELUR-001')->first();
        $mentega = Product::where('sku', 'MENTEGA-001')->first();
        $gula = Product::where('sku', 'GULA-001')->first();
        $ragi = Product::where('sku', 'RAGI-001')->first();

        // ============================================
        // BOM for Roti Tawar Jumbo (10 pcs per batch)
        // ============================================
        $bomRotiTawar = BomHeader::create([
            'product_id' => $rotiTawar->id,
            'bom_code' => 'BOM-2025-001',
            'version' => '1.0',
            'description' => 'Resep Roti Tawar Jumbo standar (10 loaf)',
            'quantity_produced' => 10, // 10 loaf roti
            'is_active' => true,
            'is_default' => true,
            'production_time_minutes' => 180, // 3 jam (mixing, proofing, baking)
            'instructions' => "1. Campur tepung, gula, ragi, dan air hangat\n2. Uleni sampai kalis (15 menit)\n3. Tambahkan mentega, uleni lagi (10 menit)\n4. Istirahatkan adonan 60 menit (proofing pertama)\n5. Bentuk adonan, masukkan loyang\n6. Proofing kedua 45 menit\n7. Panggang 180°C selama 30 menit",
        ]);

        // BOM Items for Roti Tawar
        BomItem::create([
            'bom_header_id' => $bomRotiTawar->id,
            'product_id' => $tepung->id,
            'quantity' => 5000, // 5000 gram (5 Kg)
            'waste_percentage' => 2, // 2% waste (tepung tertumpah, dll)
            'sequence' => 1,
            'notes' => 'Gunakan tepung terigu protein tinggi',
        ]);

        BomItem::create([
            'bom_header_id' => $bomRotiTawar->id,
            'product_id' => $gula->id,
            'quantity' => 500, // 500 gram
            'waste_percentage' => 0,
            'sequence' => 2,
        ]);

        BomItem::create([
            'bom_header_id' => $bomRotiTawar->id,
            'product_id' => $ragi->id,
            'quantity' => 50, // 50 gram
            'waste_percentage' => 0,
            'sequence' => 3,
            'notes' => 'Ragi instan, larutkan dengan air hangat',
        ]);

        BomItem::create([
            'bom_header_id' => $bomRotiTawar->id,
            'product_id' => $mentega->id,
            'quantity' => 400, // 400 gram
            'waste_percentage' => 1, // 1% waste
            'sequence' => 4,
            'notes' => 'Mentega suhu ruangan',
        ]);

        BomItem::create([
            'bom_header_id' => $bomRotiTawar->id,
            'product_id' => $telur->id,
            'quantity' => 300, // 300 gram (~6 butir)
            'waste_percentage' => 5, // 5% waste (kulit telur)
            'sequence' => 5,
            'notes' => 'Telur suhu ruangan, ~6 butir',
        ]);

        // ============================================
        // BOM for Croissant (20 pcs per batch)
        // ============================================
        $bomCroissant = BomHeader::create([
            'product_id' => $croissant->id,
            'bom_code' => 'BOM-2025-002',
            'version' => '1.0',
            'description' => 'Resep Croissant Original berlapis (20 pcs)',
            'quantity_produced' => 20, // 20 pcs croissant
            'is_active' => true,
            'is_default' => true,
            'production_time_minutes' => 300, // 5 jam (laminating, folding, proofing)
            'instructions' => "1. Buat dough: campur tepung, gula, ragi, susu\n2. Uleni 10 menit, dinginkan 30 menit\n3. Laminating: lipat mentega 3x (single fold)\n4. Dinginkan 30 menit setiap lipatan\n5. Roll, potong segitiga, gulung\n6. Proofing 2 jam\n7. Egg wash, panggang 200°C selama 18 menit",
        ]);

        // BOM Items for Croissant
        BomItem::create([
            'bom_header_id' => $bomCroissant->id,
            'product_id' => $tepung->id,
            'quantity' => 2500, // 2.5 Kg
            'waste_percentage' => 3, // 3% waste (lebih tinggi karena laminating)
            'sequence' => 1,
            'notes' => 'Tepung protein sedang-tinggi',
        ]);

        BomItem::create([
            'bom_header_id' => $bomCroissant->id,
            'product_id' => $mentega->id,
            'quantity' => 800, // 800 gram (untuk laminating)
            'waste_percentage' => 2, // 2% waste
            'sequence' => 2,
            'notes' => 'Mentega dingin untuk laminating, butter sheet',
        ]);

        BomItem::create([
            'bom_header_id' => $bomCroissant->id,
            'product_id' => $gula->id,
            'quantity' => 200, // 200 gram
            'waste_percentage' => 0,
            'sequence' => 3,
        ]);

        BomItem::create([
            'bom_header_id' => $bomCroissant->id,
            'product_id' => $ragi->id,
            'quantity' => 30, // 30 gram
            'waste_percentage' => 0,
            'sequence' => 4,
        ]);

        BomItem::create([
            'bom_header_id' => $bomCroissant->id,
            'product_id' => $telur->id,
            'quantity' => 150, // 150 gram (~3 butir untuk dough + egg wash)
            'waste_percentage' => 5,
            'sequence' => 5,
            'notes' => 'Untuk dough dan egg wash',
        ]);

        $this->command->info('✅ Sample BOMs seeded successfully!');
        $this->command->info('   - BOM-2025-001: Roti Tawar Jumbo (10 loaf)');
        $this->command->info('   - BOM-2025-002: Croissant Original (20 pcs)');
    }
}

