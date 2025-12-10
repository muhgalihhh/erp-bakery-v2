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
        Schema::create('customer_points_ledger', function (Blueprint $table) {
            $table->id();

            // Customer Reference
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade')->comment('Customer yang dapat/pakai poin');

            // Transaction Type
            $table->enum('type', ['earn', 'redeem', 'expire', 'adjust'])->comment('Tipe transaksi poin');

            // Points
            $table->decimal('points', 15, 2)->comment('Jumlah poin (positif untuk earn, negatif untuk redeem)');
            $table->decimal('balance_after', 15, 2)->comment('Saldo poin setelah transaksi ini');

            // Reference (Polymorphic - bisa link ke SalesOrder, Adjustment, dll)
            $table->string('referenceable_type')->nullable()->comment('Model yang terkait (SalesOrder, ManualAdjustment)');
            $table->unsignedBigInteger('referenceable_id')->nullable()->comment('ID dari model terkait');
            $table->string('reference_number')->nullable()->comment('Nomor referensi untuk display (SO-001, ADJ-001)');

            // Expiry
            $table->date('expiry_date')->nullable()->comment('Tanggal kadaluarsa poin (biasanya 1 tahun dari earn)');
            $table->boolean('is_expired')->default(false)->comment('Status kadaluarsa');

            // Description
            $table->text('description')->nullable()->comment('Deskripsi transaksi poin');

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('User yang record transaksi ini');

            $table->timestamps();

            // Indexes
            $table->index('customer_id');
            $table->index('type');
            $table->index(['customer_id', 'created_at']); // History per customer
            $table->index(['expiry_date', 'is_expired']); // Untuk cron job expire poin
            $table->index(['referenceable_type', 'referenceable_id']); // Polymorphic lookup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_points_ledger');
    }
};
