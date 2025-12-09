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
        Schema::create('manufacturing_order_materials', function (Blueprint $table) {
            $table->id();

            // Link ke Manufacturing Order
            $table->foreignId('manufacturing_order_id')
                ->constrained('manufacturing_orders')
                ->onDelete('cascade');

            // Material Information
            $table->foreignId('product_id')
                ->constrained('products')
                ->comment('Bahan baku yang digunakan');

            // Planned vs Actual
            $table->decimal('planned_quantity', 12, 4)
                ->comment('Qty dari BOM (sudah termasuk waste)');

            $table->decimal('actual_quantity', 12, 4)->default(0)
                ->comment('Qty actual yang digunakan');

            // Costing
            $table->decimal('unit_cost', 20, 4)->default(0)
                ->comment('Harga per unit saat produksi (dari inventory valuation)');

            $table->decimal('total_cost', 20, 4)->default(0)
                ->comment('actual_quantity × unit_cost');

            // Status
            $table->boolean('is_consumed')->default(false)
                ->comment('Sudah dikonsumsi/posted ke inventory?');

            $table->timestamp('consumed_at')->nullable();

            // Notes
            $table->text('notes')->nullable()
                ->comment('Catatan: substitusi bahan, dll');

            $table->timestamps();

            // Indexes
            $table->index('manufacturing_order_id');
            $table->index('product_id');
            $table->index('is_consumed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_order_materials');
    }
};
