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
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();

            // Order Reference
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade')->comment('Sales order terkait');

            // Product
            $table->foreignId('product_id')->constrained('products')->comment('Produk yang dijual');

            // Quantity & UoM
            $table->decimal('quantity', 12, 4)->comment('Jumlah barang');
            $table->string('uom', 20)->comment('Satuan penjualan (Pcs, Box, Kg, dll)');

            // Pricing
            $table->decimal('unit_price', 15, 2)->comment('Harga satuan saat transaksi (snapshot)');
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('Diskon per item (%)');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('Nilai diskon per item');
            $table->decimal('tax_percentage', 5, 2)->default(0)->comment('Pajak per item (%)');
            $table->decimal('tax_amount', 15, 2)->default(0)->comment('Nilai pajak');
            $table->decimal('subtotal', 15, 2)->comment('Subtotal (qty * unit_price)');
            $table->decimal('total', 15, 2)->comment('Total setelah diskon & pajak');

            // Costing (untuk perhitungan profit)
            $table->decimal('cost_price', 15, 2)->nullable()->comment('HPP saat transaksi (dari product.standard_cost)');
            $table->decimal('profit', 15, 2)->nullable()->comment('Laba per item (total - cost)');

            // Special Notes
            $table->text('notes')->nullable()->comment('Catatan khusus: "Tanpa gula", "Extra cream", "Tulisan: Happy Birthday"');
            $table->boolean('is_custom')->default(false)->comment('Apakah custom order (butuh produksi khusus)');

            // Fulfillment Status (untuk tracking)
            $table->enum('fulfillment_status', ['pending', 'in_production', 'ready', 'delivered'])->default('pending')->comment('Status pemenuhan item');

            $table->timestamps();

            // Indexes
            $table->index('sales_order_id');
            $table->index('product_id');
            $table->index('fulfillment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};
