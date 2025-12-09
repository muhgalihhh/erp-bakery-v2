<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();

            // Kode akun hierarkis (misal: 1-1310 untuk Persediaan Tepung)
            $table->string('code', 50)->unique();

            // Nama akun (misal: "Persediaan Bahan Baku - Tepung")
            $table->string('name');

            // Tipe akun utama untuk posisi di laporan keuangan
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);

            // Subtipe untuk klasifikasi lebih detail
            $table->enum('subtype', [
                'current_asset',        // Aset Lancar (Kas, Piutang, Persediaan)
                'fixed_asset',          // Aset Tetap (Oven, Mixer, Kendaraan)
                'current_liability',    // Kewajiban Jangka Pendek (Hutang Usaha, Poin Member)
                'long_term_liability',  // Kewajiban Jangka Panjang (Pinjaman Bank)
                'equity',               // Modal
                'cogs',                 // Harga Pokok Penjualan (Cost of Goods Sold)
                'operating_expense',    // Biaya Operasional (Gaji, Listrik, Marketing)
                'other_expense',        // Biaya Lain-lain
                'sales_revenue',        // Pendapatan Penjualan
                'other_revenue'         // Pendapatan Lain-lain
            ])->nullable();

            // Parent ID untuk struktur hierarki (nested accounts)
            // Misal: "1-1310 Persediaan Tepung" parent-nya adalah "1-1300 Persediaan Bahan Baku"
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');

            // Mata uang (default IDR, bisa multikurensi untuk impor bahan)
            $table->string('currency', 3)->default('IDR');

            // Deskripsi tambahan
            $table->text('description')->nullable();

            // Status aktif/nonaktif
            $table->boolean('is_active')->default(true);

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index('code');
            $table->index('type');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
