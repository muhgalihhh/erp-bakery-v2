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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Identitas Produk
            $table->string('sku')->unique()->comment('Stock Keeping Unit - Kode unik produk');
            $table->string('name');
            $table->text('description')->nullable();

            // Tipe Produk (Krusial untuk Bakery!)
            $table->enum('type', ['raw', 'wip', 'finished', 'service', 'consumable'])
                ->comment('raw=Bahan Baku, wip=Barang Dalam Proses, finished=Barang Jadi');

            // Flags Operasional
            $table->boolean('is_sellable')->default(false)
                ->comment('Apakah bisa dijual? (Tepung=false, Roti=true)');
            $table->boolean('is_purchasable')->default(false)
                ->comment('Apakah dibeli dari supplier? (Tepung=true, Roti=false)');

            // Multi-UoM (Unit of Measure) - PENTING!
            $table->string('uom_purchase')->default('pcs')
                ->comment('Satuan Beli: Sak, Karton, Kg');
            $table->string('uom_stock')->default('pcs')
                ->comment('Satuan Penyimpanan: Kg, Liter, Pcs');
            $table->string('uom_usage')->default('pcs')
                ->comment('Satuan Pakai di Resep: Gram, ml, Pcs');

            // Konversi UoM (Conversion Rates)
            $table->decimal('conversion_purchase_to_stock', 12, 4)->default(1)
                ->comment('Contoh: 1 Sak = 25 Kg');
            $table->decimal('conversion_stock_to_usage', 12, 4)->default(1)
                ->comment('Contoh: 1 Kg = 1000 Gram');

            // Harga & Biaya
            $table->decimal('purchase_price', 15, 2)->default(0)
                ->comment('Harga beli terakhir (untuk raw materials)');
            $table->decimal('selling_price', 15, 2)->default(0)
                ->comment('Harga jual (untuk finished goods)');
            $table->decimal('standard_cost', 15, 2)->default(0)
                ->comment('HPP standar (dihitung dari BOM)');

            // Inventory Control
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0)
                ->comment('Reorder level');
            $table->decimal('maximum_stock', 12, 2)->default(0);

            // Integrasi Akuntansi Otomatis
            $table->foreignId('income_account_id')->nullable()
                ->constrained('chart_of_accounts')
                ->comment('Akun Pendapatan (4-1100 Penjualan Roti)');
            $table->foreignId('expense_account_id')->nullable()
                ->constrained('chart_of_accounts')
                ->comment('Akun HPP (5-1100 HPP Roti)');
            $table->foreignId('inventory_account_id')->nullable()
                ->constrained('chart_of_accounts')
                ->comment('Akun Aset Persediaan (1-1310 Persediaan Bahan Baku)');

            // Metadata
            $table->boolean('is_active')->default(true);
            $table->string('barcode')->nullable();
            $table->string('image_url')->nullable();

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('sku');
            $table->index('type');
            $table->index('is_sellable');
            $table->index('is_purchasable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
