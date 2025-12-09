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
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();

            // Payment identification
            $table->string('payment_number', 50)->unique()->nullable()->comment('Auto-generated: PAY-YYYYMM-XXXX');

            // Relations
            $table->foreignId('vendor_id')->constrained()->comment('Vendor yang dibayar');
            $table->foreignId('purchase_order_id')->nullable()->constrained()->comment('PO reference (optional)');
            $table->foreignId('paid_by')->constrained('users')->comment('User yang melakukan pembayaran');

            // Payment details
            $table->date('payment_date')->comment('Tanggal pembayaran');
            $table->decimal('amount', 15, 2)->comment('Jumlah yang dibayar');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'giro', 'other'])->default('bank_transfer');
            $table->string('reference_number', 100)->nullable()->comment('No. referensi (No. Cek, No. Giro, No. Transfer)');
            $table->string('bank_account', 100)->nullable()->comment('Rekening tujuan/asal');

            // Status
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft');

            // Additional info
            $table->text('notes')->nullable();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('payment_date');
            $table->index('status');
            $table->index(['vendor_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};

