<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeederSimple extends Seeder
{
  /**
   * Simplified Chart of Accounts for Small Bakery
   * Hanya 15 akun utama yang benar-benar dipakai
   */
  public function run(): void
  {
    $accounts = [
      // ============ ASET (ASSET) - 7 akun ============
      [
        'code' => '1-0000',
        'name' => 'ASET',
        'type' => 'asset',
        'subtype' => 'current_asset',
        'description' => 'Kelompok Aset',
      ],
      [
        'code' => '1-1100',
        'name' => 'Kas Kecil',
        'type' => 'asset',
        'subtype' => 'current_asset',
        'parent_code' => '1-0000',
        'description' => 'Uang tunai di toko',
      ],
      [
        'code' => '1-1200',
        'name' => 'Kas di Bank',
        'type' => 'asset',
        'subtype' => 'current_asset',
        'parent_code' => '1-0000',
        'description' => 'Rekening bank',
      ],
      [
        'code' => '1-1300',
        'name' => 'Piutang Usaha',
        'type' => 'asset',
        'subtype' => 'current_asset',
        'parent_code' => '1-0000',
        'description' => 'Tagihan ke pelanggan/reseller',
      ],
      [
        'code' => '1-1400',
        'name' => 'Persediaan Bahan Baku',
        'type' => 'asset',
        'subtype' => 'current_asset',
        'parent_code' => '1-0000',
        'description' => 'Tepung, Gula, Telur, Mentega, dll',
      ],
      [
        'code' => '1-1500',
        'name' => 'Persediaan Barang Jadi',
        'type' => 'asset',
        'subtype' => 'current_asset',
        'parent_code' => '1-0000',
        'description' => 'Roti, Kue siap jual',
      ],
      [
        'code' => '1-2000',
        'name' => 'Peralatan & Mesin',
        'type' => 'asset',
        'subtype' => 'fixed_asset',
        'parent_code' => '1-0000',
        'description' => 'Oven, Mixer, Showcase, dll',
      ],

      // ============ KEWAJIBAN (LIABILITY) - 2 akun ============
      [
        'code' => '2-0000',
        'name' => 'KEWAJIBAN',
        'type' => 'liability',
        'subtype' => 'current_liability',
        'description' => 'Kelompok Kewajiban',
      ],
      [
        'code' => '2-1100',
        'name' => 'Hutang Usaha',
        'type' => 'liability',
        'subtype' => 'current_liability',
        'parent_code' => '2-0000',
        'description' => 'Hutang ke supplier bahan baku',
      ],

      // ============ MODAL (EQUITY) - 1 akun ============
      [
        'code' => '3-0000',
        'name' => 'MODAL',
        'type' => 'equity',
        'subtype' => 'equity',
        'description' => 'Kelompok Modal',
      ],
      [
        'code' => '3-1000',
        'name' => 'Modal Pemilik',
        'type' => 'equity',
        'subtype' => 'equity',
        'parent_code' => '3-0000',
      ],

      // ============ PENDAPATAN (REVENUE) - 2 akun ============
      [
        'code' => '4-0000',
        'name' => 'PENDAPATAN',
        'type' => 'revenue',
        'subtype' => 'sales_revenue',
        'description' => 'Kelompok Pendapatan',
      ],
      [
        'code' => '4-1000',
        'name' => 'Pendapatan Penjualan',
        'type' => 'revenue',
        'subtype' => 'sales_revenue',
        'parent_code' => '4-0000',
        'description' => 'Penjualan roti & kue',
      ],

      // ============ BIAYA (EXPENSE) - 3 akun ============
      [
        'code' => '5-0000',
        'name' => 'BIAYA',
        'type' => 'expense',
        'subtype' => 'cogs',
        'description' => 'Kelompok Biaya',
      ],
      [
        'code' => '5-1000',
        'name' => 'Harga Pokok Penjualan (HPP)',
        'type' => 'expense',
        'subtype' => 'cogs',
        'parent_code' => '5-0000',
        'description' => 'Cost barang yang terjual',
      ],
      [
        'code' => '5-2000',
        'name' => 'Biaya Gaji Karyawan',
        'type' => 'expense',
        'subtype' => 'operating_expense',
        'parent_code' => '5-0000',
      ],
      [
        'code' => '5-3000',
        'name' => 'Biaya Listrik & Gas',
        'type' => 'expense',
        'subtype' => 'operating_expense',
        'parent_code' => '5-0000',
        'description' => 'Untuk oven dan operasional',
      ],
    ];

    foreach ($accounts as $accountData) {
      $parentCode = $accountData['parent_code'] ?? null;
      $parentId = null;

      if ($parentCode) {
        $parent = ChartOfAccount::where('code', $parentCode)->first();
        if ($parent) {
          $parentId = $parent->id;
        }
      }

      ChartOfAccount::create([
        'code' => $accountData['code'],
        'name' => $accountData['name'],
        'type' => $accountData['type'],
        'subtype' => $accountData['subtype'] ?? null,
        'parent_id' => $parentId,
        'description' => $accountData['description'] ?? null,
        'is_active' => true,
      ]);
    }

    $this->command->info('✅ Simplified Chart of Accounts seeded successfully (17 accounts)');
  }
}
