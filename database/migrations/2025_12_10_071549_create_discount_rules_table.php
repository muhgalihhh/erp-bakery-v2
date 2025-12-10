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
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->id();

            // Rule Identification
            $table->string('name', 100)->comment('Nama promosi: "Grand Opening 50%", "Buy 1 Get 1 Croissant"');
            $table->string('code', 50)->unique()->nullable()->comment('Kode unik untuk referensi');
            $table->text('description')->nullable()->comment('Deskripsi detail promosi');

            // Coupon Code (Optional - untuk voucher)
            $table->string('coupon_code', 50)->unique()->nullable()->comment('Kode voucher yang harus diinput customer');
            $table->boolean('is_public')->default(true)->comment('Publik (semua customer) atau private (pakai coupon code)');

            // Schedule
            $table->dateTime('start_date')->comment('Mulai berlaku');
            $table->dateTime('end_date')->comment('Berakhir');

            // Priority (jika ada multiple discount yang eligible)
            $table->integer('priority')->default(0)->comment('Semakin tinggi semakin prioritas (0 = lowest)');
            $table->boolean('can_combine')->default(false)->comment('Bisa dikombinasi dengan discount lain?');

            // Conditions (JSON - logika kapan discount ini berlaku)
            // Contoh:
            // {
            //   "min_subtotal": 100000,
            //   "max_subtotal": 500000,
            //   "required_product_ids": [1, 2, 3],
            //   "required_category_ids": [1],
            //   "customer_tier_codes": ["gold", "platinum"],
            //   "day_of_week": ["Saturday", "Sunday"],
            //   "time_range": {"from": "14:00", "to": "17:00"},
            //   "min_quantity": 5,
            //   "first_purchase_only": false
            // }
            $table->json('conditions')->nullable()->comment('Kondisi yang harus dipenuhi (JSON format)');

            // Actions (JSON - apa yang terjadi jika kondisi terpenuhi)
            // Contoh:
            // {
            //   "type": "percent_off",  // atau "amount_off", "buy_x_get_y", "free_shipping"
            //   "value": 10,
            //   "max_discount_amount": 50000,
            //   "apply_to": "all", // atau "cheapest", "most_expensive", "specific_products"
            //   "free_product_id": null,
            //   "free_quantity": 1
            // }
            $table->json('actions')->comment('Aksi yang dijalankan (JSON format)');

            // Usage Limits
            $table->integer('usage_limit')->nullable()->comment('Max berapa kali bisa dipakai (NULL = unlimited)');
            $table->integer('usage_limit_per_customer')->nullable()->comment('Max berapa kali per customer');
            $table->integer('usage_count')->default(0)->comment('Counter berapa kali sudah dipakai');

            // Status
            $table->boolean('is_active')->default(true)->comment('Status aktif discount');

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('coupon_code');
            $table->index(['is_active', 'start_date', 'end_date']); // Active discount lookup
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_rules');
    }
};
