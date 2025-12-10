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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();

            // Order Identification
            $table->string('order_number', 50)->unique()->nullable()->comment('Auto-generated: SO-YYYYMM-XXXX');

            // Customer (nullable untuk walk-in customer tanpa member)
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->comment('Customer (NULL = walk-in tanpa member)');

            // Order Details
            $table->dateTime('order_date')->comment('Tanggal & waktu order');
            $table->enum('order_type', ['pos', 'online', 'catering', 'preorder', 'wholesale'])->default('pos')->comment('Jenis order');
            $table->enum('order_channel', ['store', 'whatsapp', 'instagram', 'website', 'phone'])->default('store')->comment('Channel penjualan');

            // Delivery Info (untuk catering/preorder)
            $table->date('delivery_date')->nullable()->comment('Tanggal pengiriman/pickup');
            $table->time('delivery_time')->nullable()->comment('Jam pengiriman/pickup');
            $table->text('delivery_address')->nullable()->comment('Alamat pengiriman');
            $table->string('delivery_phone', 20)->nullable()->comment('No HP penerima');
            $table->string('delivery_recipient', 100)->nullable()->comment('Nama penerima');

            // Order Status Flow
            $table->enum('status', [
                'draft',           // Belum dikonfirmasi
                'confirmed',       // Confirmed, waiting for production/pickup
                'in_production',   // Sedang diproduksi (untuk custom order)
                'ready',           // Siap diambil/dikirim
                'delivered',       // Sudah dikirim/diambil
                'completed',       // Completed (customer puas)
                'cancelled'        // Dibatalkan
            ])->default('draft')->comment('Status order');

            // Financial Details
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Subtotal sebelum diskon & pajak');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('Total diskon');
            $table->string('discount_source', 100)->nullable()->comment('Sumber diskon (nama rule atau manual)');
            $table->decimal('tax_percentage', 5, 2)->default(0)->comment('Persentase pajak (PPN 11%)');
            $table->decimal('tax_amount', 15, 2)->default(0)->comment('Nilai pajak');
            $table->decimal('shipping_cost', 15, 2)->default(0)->comment('Biaya pengiriman');
            $table->decimal('points_used', 15, 2)->default(0)->comment('Poin yang ditukar (nilai rupiah)');
            $table->decimal('points_earned', 15, 2)->default(0)->comment('Poin yang didapat dari order ini');
            $table->decimal('total', 15, 2)->default(0)->comment('Total akhir yang harus dibayar');

            // Payment Info
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending')->comment('Status pembayaran');
            $table->enum('payment_method', [
                'cash',
                'credit_card',
                'debit_card',
                'e_wallet',
                'bank_transfer',
                'qris',
                'split'  // Untuk pembayaran kombinasi
            ])->nullable()->comment('Metode pembayaran');
            $table->decimal('paid_amount', 15, 2)->default(0)->comment('Jumlah yang sudah dibayar');
            $table->decimal('change_amount', 15, 2)->default(0)->comment('Uang kembalian (untuk cash)');
            $table->json('payment_details')->nullable()->comment('Detail pembayaran split/multiple method (JSON)');

            // Staff & Service
            $table->foreignId('served_by')->nullable()->constrained('users')->nullOnDelete()->comment('Kasir/staff yang melayani');
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete()->comment('Baker yang memproduksi (untuk custom order)');

            // Notes & Special Requests
            $table->text('customer_notes')->nullable()->comment('Catatan dari customer (tulisan kue, alergi, dll)');
            $table->text('internal_notes')->nullable()->comment('Catatan internal (prioritas, special handling)');

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('order_number');
            $table->index('customer_id');
            $table->index('order_date');
            $table->index(['status', 'order_date']);
            $table->index(['payment_status', 'order_date']);
            $table->index('order_type');
            $table->index('delivery_date');
            $table->index('served_by'); // Performance report per kasir
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
