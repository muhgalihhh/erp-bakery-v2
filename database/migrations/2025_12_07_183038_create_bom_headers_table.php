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
        Schema::create('bom_headers', function (Blueprint $table) {
            $table->id();

            // Produk yang akan dibuat
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade')
                ->comment('Produk hasil (misal: Roti Tawar)');

            // Informasi Resep
            $table->string('bom_code')->unique()->comment('Kode resep: BOM-RT-001');
            $table->string('version')->default('1.0')->comment('Versi resep (untuk tracking perubahan)');
            $table->text('description')->nullable();

            // Yield/Output
            $table->decimal('quantity_produced', 12, 4)->default(1)
                ->comment('Jumlah yang dihasilkan dari resep ini (misal: 10 pcs roti)');

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(true)
                ->comment('Apakah ini resep default untuk produk ini?');

            // Manufacturing Info
            $table->integer('production_time_minutes')->nullable()
                ->comment('Estimasi waktu produksi (menit)');
            $table->text('instructions')->nullable()
                ->comment('Instruksi pembuatan step-by-step');

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('product_id');
            $table->index('bom_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_headers');
    }
};
