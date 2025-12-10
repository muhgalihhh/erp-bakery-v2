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
        Schema::create('customer_tiers', function (Blueprint $table) {
            $table->id();

            // Tier Information
            $table->string('name', 50)->unique()->comment('Nama tier: Bronze, Silver, Gold, Platinum, VIP');
            $table->string('code', 20)->unique()->comment('Kode tier untuk internal use');
            $table->text('description')->nullable()->comment('Deskripsi benefit tier');

            // Requirements
            $table->decimal('minimum_spend', 15, 2)->default(0)->comment('Min total pembelian untuk naik ke tier ini');

            // Benefits
            $table->decimal('point_multiplier', 5, 2)->default(1.00)->comment('Multiplier untuk earning points (1.0 = normal, 1.5 = 50% bonus, 2.0 = double)');
            $table->decimal('discount_percentage', 5, 2)->default(0)->comment('Diskon otomatis untuk tier ini (%)');
            $table->json('benefits')->nullable()->comment('Daftar benefit dalam JSON format');

            // Settings
            $table->integer('priority')->default(0)->comment('Urutan prioritas tier (semakin tinggi semakin istimewa)');
            $table->string('color', 20)->nullable()->comment('Warna badge untuk UI (primary, success, warning, danger)');
            $table->string('icon', 50)->nullable()->comment('Icon heroicon untuk UI');
            $table->boolean('is_active')->default(true)->comment('Status aktif tier');

            $table->timestamps();

            // Indexes
            $table->index('is_active');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_tiers');
    }
};
