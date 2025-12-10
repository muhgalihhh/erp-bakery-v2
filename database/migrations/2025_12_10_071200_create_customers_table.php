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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Customer Identification
            $table->string('customer_code', 50)->unique()->nullable()->comment('Auto-generated: CUST-XXXX');
            $table->string('name', 100)->comment('Nama customer');
            $table->string('phone', 20)->unique()->comment('Nomor HP - kunci identifikasi utama untuk retail');
            $table->string('email', 100)->nullable()->comment('Email customer');

            // Personal Info
            $table->date('date_of_birth')->nullable()->comment('Tanggal lahir untuk diskon ulang tahun');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('Jenis kelamin');
            $table->text('address')->nullable()->comment('Alamat lengkap');
            $table->string('city', 50)->nullable()->comment('Kota');
            $table->string('province', 50)->nullable()->comment('Provinsi');
            $table->string('postal_code', 10)->nullable()->comment('Kode pos');

            // Loyalty & Tier
            $table->foreignId('customer_tier_id')->nullable()->constrained('customer_tiers')->nullOnDelete()->comment('Tier customer (Bronze/Silver/Gold/Platinum/VIP)');
            $table->decimal('total_points', 15, 2)->default(0)->comment('Saldo poin loyalitas saat ini');
            $table->decimal('total_spent', 15, 2)->default(0)->comment('Total pembelian sepanjang masa (untuk auto-upgrade tier)');
            $table->integer('transaction_count')->default(0)->comment('Jumlah transaksi');
            $table->date('last_purchase_date')->nullable()->comment('Tanggal pembelian terakhir');

            // Social Media (untuk marketing)
            $table->string('instagram', 100)->nullable();
            $table->string('facebook', 100)->nullable();

            // Preferences
            $table->json('preferences')->nullable()->comment('Preferensi customer (favorit produk, alergi, dll) dalam JSON');
            $table->boolean('subscribe_newsletter')->default(false)->comment('Subscribe email marketing');
            $table->boolean('subscribe_whatsapp')->default(true)->comment('Subscribe WA broadcast');

            // Status & Notes
            $table->boolean('is_active')->default(true)->comment('Status aktif customer');
            $table->text('notes')->nullable()->comment('Catatan internal tentang customer');

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performance
            $table->index('phone'); // Most common lookup
            $table->index('customer_tier_id');
            $table->index(['is_active', 'customer_tier_id']);
            $table->index('total_spent'); // For tier auto-upgrade
            $table->index('last_purchase_date'); // For inactive customer report
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
