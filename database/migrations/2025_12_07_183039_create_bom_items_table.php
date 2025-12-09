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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();

            // Link ke Header BOM
            $table->foreignId('bom_header_id')
                ->constrained('bom_headers')
                ->onDelete('cascade');

            // Bahan baku yang digunakan
            $table->foreignId('product_id')
                ->constrained('products')
                ->comment('Bahan baku (misal: Tepung, Telur)');

            // Kuantitas
            $table->decimal('quantity', 12, 4)
                ->comment('Jumlah bahan yang dibutuhkan (dalam UoM usage)');

            // Waste Management (PENTING untuk Bakery!)
            $table->decimal('waste_percentage', 5, 2)->default(0)
                ->comment('Estimasi terbuang (kulit telur=5%, tepung tumpah=2%)');

            // Calculated Fields
            $table->decimal('quantity_with_waste', 12, 4)->storedAs('quantity * (1 + waste_percentage / 100)')
                ->comment('Quantity actual yang harus disiapkan termasuk waste');

            // Urutan Penggunaan (untuk instruksi)
            $table->integer('sequence')->default(0)
                ->comment('Urutan penggunaan bahan (1=pertama, dst)');

            // Notes spesifik per bahan
            $table->text('notes')->nullable()
                ->comment('Catatan khusus: "Kocok telur dulu", "Ayak tepung"');

            $table->timestamps();

            // Indexes
            $table->index('bom_header_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
