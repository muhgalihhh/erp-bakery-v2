<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'vendor_code' => 'SUP-001',
                'name' => 'PT Bogasari Flour Mills',
                'contact_person' => 'Budi Santoso',
                'phone' => '021-5551234',
                'email' => 'sales@bogasari.co.id',
                'address' => 'Jl. Inspeksi Kalimalang No. 1, Jakarta Timur, DKI Jakarta 13450',
                'tax_id' => '01.234.567.8-901.000',
                'payment_terms_days' => 30,
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'PT Bogasari Flour Mills Indonesia',
                'is_active' => true,
                'notes' => 'Supplier utama tepung terigu berkualitas tinggi. Credit Limit: Rp 50,000,000',
            ],
            [
                'vendor_code' => 'SUP-002',
                'name' => 'CV Mitra Telur Sejahtera',
                'contact_person' => 'Siti Nurhaliza',
                'phone' => '021-8765432',
                'email' => 'order@mitratelur.com',
                'address' => 'Jl. Raya Pasar Minggu No. 45, Jakarta Selatan 12510',
                'tax_id' => '02.345.678.9-012.000',
                'payment_terms_days' => 14,
                'bank_name' => 'Bank BCA',
                'bank_account_number' => '5678901234',
                'bank_account_name' => 'CV Mitra Telur Sejahtera',
                'is_active' => true,
                'notes' => 'Supplier telur ayam negeri fresh daily. Credit Limit: Rp 20,000,000',
            ],
            [
                'vendor_code' => 'SUP-003',
                'name' => 'PT Frisian Flag Indonesia',
                'contact_person' => 'Ahmad Fauzi',
                'phone' => '021-4567890',
                'email' => 'b2b@frisianflag.com',
                'address' => 'Jl. Raya Jakarta-Bogor Km. 23, Jakarta Timur 13720',
                'tax_id' => '03.456.789.0-123.000',
                'payment_terms_days' => 45,
                'bank_name' => 'Bank BNI',
                'bank_account_number' => '9876543210',
                'bank_account_name' => 'PT Frisian Flag Indonesia',
                'is_active' => true,
                'notes' => 'Supplier susu dan produk dairy. Credit Limit: Rp 30,000,000',
            ],
            [
                'vendor_code' => 'SUP-004',
                'name' => 'Toko Bahan Kue Sinar Jaya',
                'contact_person' => 'Tjandra Wijaya',
                'phone' => '021-3334567',
                'email' => 'sinarjaya@gmail.com',
                'address' => 'Jl. Glodok Plaza Blok F No. 12, Jakarta Barat 11180',
                'tax_id' => '04.567.890.1-234.000',
                'payment_terms_days' => 7,
                'bank_name' => 'Bank BRI',
                'bank_account_number' => '3456789012',
                'bank_account_name' => 'Toko Bahan Kue Sinar Jaya',
                'is_active' => true,
                'notes' => 'Supplier bahan kue, coklat, essence, dll. Credit Limit: Rp 10,000,000',
            ],
            [
                'vendor_code' => 'SUP-005',
                'name' => 'CV Gula Manis Sentosa',
                'contact_person' => 'Dewi Lestari',
                'phone' => '021-7778888',
                'email' => 'order@gulamanis.co.id',
                'address' => 'Jl. Industri Raya No. 88, Tangerang, Banten 15134',
                'tax_id' => '05.678.901.2-345.000',
                'payment_terms_days' => 21,
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '7890123456',
                'bank_account_name' => 'CV Gula Manis Sentosa',
                'is_active' => true,
                'notes' => 'Supplier gula pasir dan gula aren. Credit Limit: Rp 25,000,000',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }

        $this->command->info('✅ 5 Sample vendors created successfully!');
    }
}
