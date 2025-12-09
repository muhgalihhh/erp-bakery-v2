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
        Schema::create('journal_postings', function (Blueprint $table) {
            $table->id();

            // Link ke header journal entry
            $table->foreignUuid('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');

            // Link ke akun di chart of accounts
            $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('restrict');

            // Debit amount (gunakan DECIMAL untuk presisi keuangan, bukan FLOAT!)
            // 20 digit total, 4 digit desimal (cukup untuk Rp 9,999,999,999,999.9999)
            $table->decimal('debit', 20, 4)->default(0);

            // Credit amount
            $table->decimal('credit', 20, 4)->default(0);

            // Keterangan spesifik untuk baris ini (opsional)
            $table->string('line_description')->nullable();

            // JSON untuk cost center atau tracking dimensi lain
            // Misal: {"cost_center": "Produksi Roti", "project": "Pesanan Katering Wedding A"}
            $table->json('tags')->nullable();

            $table->timestamps();

            // Index untuk agregasi balance
            $table->index('journal_entry_id');
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_postings');
    }
};
