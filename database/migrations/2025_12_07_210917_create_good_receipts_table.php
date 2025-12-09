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
        Schema::create('good_receipts', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->constrained('users')->comment('User who received the goods');

            // Receipt Details
            $table->string('receipt_number')->unique();
            $table->date('receipt_date');

            // Status: draft, confirmed, cancelled
            $table->string('status')->default('draft');

            // Notes
            $table->text('notes')->nullable();
            $table->text('delivery_note_number')->nullable()->comment('Surat jalan dari vendor');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('good_receipts');
    }
};
