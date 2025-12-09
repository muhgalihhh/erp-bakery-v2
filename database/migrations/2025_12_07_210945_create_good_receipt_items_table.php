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
        Schema::create('good_receipt_items', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('good_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();

            // Quantities
            $table->decimal('ordered_quantity', 15, 4)->comment('Quantity dari PO');
            $table->decimal('received_quantity', 15, 4)->comment('Quantity yang diterima');
            $table->decimal('rejected_quantity', 15, 4)->default(0)->comment('Quantity yang ditolak/rusak');

            // Notes
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable()->comment('Alasan reject jika ada');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_receipt_items');
    }
};
