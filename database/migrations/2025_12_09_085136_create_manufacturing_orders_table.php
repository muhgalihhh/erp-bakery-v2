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
        Schema::create('manufacturing_orders', function (Blueprint $table) {
            $table->id();

            // Manufacturing Order Information
            $table->string('mo_number')->unique()->comment('MO-YYYYMM-XXXX');
            $table->date('production_date')->comment('Tanggal produksi');
            $table->date('planned_start_date')->nullable();
            $table->date('planned_finish_date')->nullable();
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_finish_time')->nullable();

            // Product to Manufacture
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict')
                ->comment('Produk yang akan diproduksi');

            $table->foreignId('bom_header_id')
                ->constrained('bom_headers')
                ->onDelete('restrict')
                ->comment('BOM yang digunakan');

            // Quantities
            $table->decimal('quantity_to_produce', 12, 4)
                ->comment('Target produksi (planned)');

            $table->decimal('quantity_produced', 12, 4)->default(0)
                ->comment('Actual quantity produced');

            $table->decimal('quantity_scrapped', 12, 4)->default(0)
                ->comment('Quantity rusak/reject');

            // Status
            $table->enum('status', [
                'draft',
                'confirmed',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('draft');

            // Costing (will be calculated)
            $table->decimal('material_cost', 20, 4)->default(0)
                ->comment('Total biaya bahan baku actual');

            $table->decimal('labor_cost', 20, 4)->default(0)
                ->comment('Biaya tenaga kerja');

            $table->decimal('overhead_cost', 20, 4)->default(0)
                ->comment('Overhead (listrik, gas, depresiasi)');

            $table->decimal('total_cost', 20, 4)->default(0)
                ->comment('Total HPP');

            $table->decimal('cost_per_unit', 20, 4)->nullable()
                ->comment('HPP per unit (total_cost / quantity_produced)');

            // Work Location
            $table->string('work_center')->nullable()
                ->comment('Area produksi: Oven-1, Mixer-2, dll');

            // Responsible
            $table->foreignId('supervisor_id')->nullable()
                ->constrained('users')
                ->comment('PIC/Supervisor produksi');

            // Notes
            $table->text('notes')->nullable();
            $table->text('completion_notes')->nullable()
                ->comment('Catatan saat selesai produksi');

            // Audit Trail
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->foreignId('completed_by')->nullable()->constrained('users');

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('mo_number');
            $table->index('production_date');
            $table->index('status');
            $table->index('product_id');
            $table->index('bom_header_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_orders');
    }
};
