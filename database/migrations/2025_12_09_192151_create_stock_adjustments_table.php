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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Adjustment Number (auto-generated)
            $table->string('adjustment_number', 50)->unique();

            // Adjustment Date
            $table->date('adjustment_date')->index();

            // Product being adjusted
            $table->foreignUuid('product_id')->constrained('products')->onDelete('restrict');

            // Type: STOCK_OPNAME (cek fisik), DAMAGED (rusak), EXPIRED (kadaluarsa), OTHER (lainnya)
            $table->enum('type', ['STOCK_OPNAME', 'DAMAGED', 'EXPIRED', 'OTHER'])->index();

            // Quantity Adjustment
            $table->decimal('system_quantity', 15, 4); // Qty di sistem sebelum adjustment
            $table->decimal('actual_quantity', 15, 4); // Qty hasil cek fisik/aktual
            $table->decimal('difference_quantity', 15, 4); // Selisih (actual - system), bisa + atau -
            $table->string('uom', 20); // Unit of Measure

            // Reason & Notes
            $table->enum('reason', [
                'STOCK_COUNT', // Stock opname/cek fisik
                'DAMAGED', // Rusak
                'EXPIRED', // Kadaluarsa
                'LOST', // Hilang
                'FOUND', // Ketemu
                'OTHER' // Lainnya
            ])->index();
            $table->text('notes')->nullable();

            // Status
            $table->enum('status', ['DRAFT', 'APPROVED', 'CANCELLED'])->default('DRAFT')->index();

            // User tracking
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users')->nullOnDelete();

            // Indexes
            $table->index(['product_id', 'adjustment_date']);
            $table->index(['status', 'adjustment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
