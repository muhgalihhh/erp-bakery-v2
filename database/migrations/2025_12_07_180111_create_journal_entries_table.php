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
        Schema::create('journal_entries', function (Blueprint $table) {
            // UUID untuk keamanan ID pada sistem terdistribusi
            $table->uuid('id')->primary();

            // Nomor transaksi yang human-readable (misal: JE-2025-12-001)
            $table->string('transaction_number')->unique();

            // Tanggal posting (kapan transaksi dicatat)
            $table->date('posting_date');

            // Deskripsi transaksi
            $table->text('description');

            // Polymorphic relationship ke dokumen sumber
            // Bisa link ke: Order (Penjualan), Purchase (Pembelian), ManufacturingOrder (Produksi), Payroll (Gaji)
            $table->nullableMorphs('referenceable');

            // Status jurnal: draft (belum final) atau posted (sudah dikunci)
            $table->boolean('is_posted')->default(false);

            // Tanggal dikunci (tidak bisa diedit lagi)
            $table->timestamp('posted_at')->nullable();

            // User yang membuat & memposting
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('posted_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa
            $table->index('transaction_number');
            $table->index('posting_date');
            // nullableMorphs() sudah auto-create index, tidak perlu manual lagi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
