<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // ============ ASET (ASSET) ============
            [
                'code' => '1-0000',
                'name' => 'ASET',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'description' => 'Kelompok Aset',
            ],

            // Aset Lancar
            [
                'code' => '1-1000',
                'name' => 'ASET LANCAR',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-0000',
            ],
            [
                'code' => '1-1100',
                'name' => 'Kas dan Bank',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1000',
            ],
            [
                'code' => '1-1110',
                'name' => 'Kas Kecil',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1100',
                'description' => 'Uang tunai di toko',
            ],
            [
                'code' => '1-1120',
                'name' => 'Kas di Bank',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1100',
                'description' => 'Rekening bank perusahaan',
            ],

            // Persediaan (PENTING untuk Bakery!)
            [
                'code' => '1-1300',
                'name' => 'PERSEDIAAN',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1000',
            ],
            [
                'code' => '1-1310',
                'name' => 'Persediaan Bahan Baku',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1300',
                'description' => 'Tepung, Gula, Telur, Ragi, Mentega, dll',
            ],
            [
                'code' => '1-1320',
                'name' => 'Persediaan Barang Dalam Proses (WIP)',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1300',
                'description' => 'Adonan beku, filling yang sudah dimasak',
            ],
            [
                'code' => '1-1330',
                'name' => 'Persediaan Barang Jadi',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1300',
                'description' => 'Roti, Kue, Pastry siap jual',
            ],
            [
                'code' => '1-1340',
                'name' => 'Persediaan Bahan Kemasan',
                'type' => 'asset',
                'subtype' => 'current_asset',
                'parent_code' => '1-1300',
                'description' => 'Dus, plastik, sticker, ribbon',
            ],

            // Aset Tetap
            [
                'code' => '1-2000',
                'name' => 'ASET TETAP',
                'type' => 'asset',
                'subtype' => 'fixed_asset',
                'parent_code' => '1-0000',
            ],
            [
                'code' => '1-2100',
                'name' => 'Mesin dan Peralatan Produksi',
                'type' => 'asset',
                'subtype' => 'fixed_asset',
                'parent_code' => '1-2000',
                'description' => 'Oven, Mixer, Chiller, Proofer',
            ],

            // ============ KEWAJIBAN (LIABILITY) ============
            [
                'code' => '2-0000',
                'name' => 'KEWAJIBAN',
                'type' => 'liability',
                'subtype' => 'current_liability',
            ],
            [
                'code' => '2-1000',
                'name' => 'KEWAJIBAN LANCAR',
                'type' => 'liability',
                'subtype' => 'current_liability',
                'parent_code' => '2-0000',
            ],
            [
                'code' => '2-1100',
                'name' => 'Hutang Usaha',
                'type' => 'liability',
                'subtype' => 'current_liability',
                'parent_code' => '2-1000',
                'description' => 'Hutang ke supplier bahan baku',
            ],
            [
                'code' => '2-1200',
                'name' => 'Kewajiban Poin Member (Loyalty Points)',
                'type' => 'liability',
                'subtype' => 'current_liability',
                'parent_code' => '2-1000',
                'description' => 'Poin yang belum ditukar pelanggan',
            ],

            // ============ MODAL (EQUITY) ============
            [
                'code' => '3-0000',
                'name' => 'MODAL',
                'type' => 'equity',
                'subtype' => 'equity',
            ],
            [
                'code' => '3-1000',
                'name' => 'Modal Pemilik',
                'type' => 'equity',
                'subtype' => 'equity',
                'parent_code' => '3-0000',
            ],

            // ============ PENDAPATAN (REVENUE) ============
            [
                'code' => '4-0000',
                'name' => 'PENDAPATAN',
                'type' => 'revenue',
                'subtype' => 'sales_revenue',
            ],
            [
                'code' => '4-1000',
                'name' => 'PENDAPATAN PENJUALAN',
                'type' => 'revenue',
                'subtype' => 'sales_revenue',
                'parent_code' => '4-0000',
            ],
            [
                'code' => '4-1100',
                'name' => 'Penjualan Roti',
                'type' => 'revenue',
                'subtype' => 'sales_revenue',
                'parent_code' => '4-1000',
            ],
            [
                'code' => '4-1200',
                'name' => 'Penjualan Kue & Pastry',
                'type' => 'revenue',
                'subtype' => 'sales_revenue',
                'parent_code' => '4-1000',
            ],
            [
                'code' => '4-1300',
                'name' => 'Penjualan Custom Order (Wedding Cake, dll)',
                'type' => 'revenue',
                'subtype' => 'sales_revenue',
                'parent_code' => '4-1000',
            ],

            // ============ BIAYA (EXPENSE) ============
            [
                'code' => '5-0000',
                'name' => 'BIAYA',
                'type' => 'expense',
                'subtype' => 'cogs',
            ],

            // Harga Pokok Penjualan (COGS)
            [
                'code' => '5-1000',
                'name' => 'HARGA POKOK PENJUALAN (HPP)',
                'type' => 'expense',
                'subtype' => 'cogs',
                'parent_code' => '5-0000',
            ],
            [
                'code' => '5-1100',
                'name' => 'HPP - Roti',
                'type' => 'expense',
                'subtype' => 'cogs',
                'parent_code' => '5-1000',
                'description' => 'Biaya bahan baku untuk roti yang terjual',
            ],
            [
                'code' => '5-1200',
                'name' => 'HPP - Kue & Pastry',
                'type' => 'expense',
                'subtype' => 'cogs',
                'parent_code' => '5-1000',
            ],

            // Biaya Operasional
            [
                'code' => '5-2000',
                'name' => 'BIAYA OPERASIONAL',
                'type' => 'expense',
                'subtype' => 'operating_expense',
                'parent_code' => '5-0000',
            ],
            [
                'code' => '5-2100',
                'name' => 'Biaya Gaji Karyawan',
                'type' => 'expense',
                'subtype' => 'operating_expense',
                'parent_code' => '5-2000',
            ],
            [
                'code' => '5-2200',
                'name' => 'Biaya Listrik & Gas',
                'type' => 'expense',
                'subtype' => 'operating_expense',
                'parent_code' => '5-2000',
                'description' => 'Untuk oven dan operasional produksi',
            ],
            [
                'code' => '5-2300',
                'name' => 'Biaya Marketing & Promosi',
                'type' => 'expense',
                'subtype' => 'operating_expense',
                'parent_code' => '5-2000',
            ],
            [
                'code' => '5-2400',
                'name' => 'Biaya Loyalitas & Diskon',
                'type' => 'expense',
                'subtype' => 'operating_expense',
                'parent_code' => '5-2000',
                'description' => 'Expense saat member dapat poin',
            ],
        ];

        foreach ($accounts as $accountData) {
            $parentCode = $accountData['parent_code'] ?? null;
            unset($accountData['parent_code']);

            $parentId = null;
            if ($parentCode) {
                $parent = ChartOfAccount::where('code', $parentCode)->first();
                $parentId = $parent?->id;
            }

            ChartOfAccount::create(array_merge($accountData, [
                'parent_id' => $parentId,
                'is_active' => true,
            ]));
        }

        $this->command->info('✅ ' . count($accounts) . ' akun berhasil di-seed!');
        $this->command->info('📊 Bagan Akun Bakery siap digunakan');
    }
}

