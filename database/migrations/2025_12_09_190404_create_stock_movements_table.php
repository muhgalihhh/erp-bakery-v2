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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Movement Type: IN (masuk), OUT (keluar), ADJUSTMENT (penyesuaian)
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT'])->index();

            // Polymorphic relation to source transaction (ManufacturingOrder, GoodReceipt, SalesOrder, StockAdjustment, etc)
            $table->string('reference_type')->nullable()->index(); // App\Models\GoodReceipt
            $table->uuid('reference_id')->nullable()->index(); // UUID of GoodReceipt/MO/etc
            $table->string('reference_number')->nullable(); // For display: GR-001, MO-001, etc

            // Product & Quantity
            $table->foreignUuid('product_id')->constrained('products')->onDelete('restrict');
            $table->decimal('quantity', 15, 4); // Positive for IN, Negative for OUT
            $table->string('uom', 20); // Unit of Measure: kg, pcs, box, etc

            // Balance AFTER this movement (untuk cek history)
            $table->decimal('balance_after', 15, 4)->default(0);

            // Optional: Warehouse/Location (future enhancement)
            $table->string('warehouse_code')->nullable()->index();

            // Batch/Lot Tracking (penting untuk bakery - expired date)
            $table->string('batch_number')->nullable();
            $table->date('expired_date')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // User tracking
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // Indexes for faster queries
            $table->index(['product_id', 'created_at']); // For product movement history
            $table->index(['type', 'created_at']); // For filtering by type and date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
