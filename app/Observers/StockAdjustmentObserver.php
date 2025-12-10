<?php

namespace App\Observers;

use App\Models\StockAdjustment;

class StockAdjustmentObserver
{
    /**
     * Handle the StockAdjustment "creating" event.
     */
    public function creating(StockAdjustment $stockAdjustment): void
    {
        // Auto-generate adjustment number jika belum ada
        if (empty($stockAdjustment->adjustment_number)) {
            $stockAdjustment->adjustment_number = $this->generateAdjustmentNumber();
        }

        // Auto-fill system quantity dari current stock produk
        if (empty($stockAdjustment->system_quantity) && $stockAdjustment->product) {
            $stockAdjustment->system_quantity = $stockAdjustment->product->current_stock;
        }

        // Auto-calculate difference
        if (!empty($stockAdjustment->actual_quantity) && !empty($stockAdjustment->system_quantity)) {
            $stockAdjustment->difference_quantity = $stockAdjustment->actual_quantity - $stockAdjustment->system_quantity;
        }

        // Auto-fill UOM dari produk
        if (empty($stockAdjustment->uom) && $stockAdjustment->product) {
            $stockAdjustment->uom = $stockAdjustment->product->uom_stock;
        }
    }

    /**
     * Handle the StockAdjustment "updating" event.
     */
    public function updating(StockAdjustment $stockAdjustment): void
    {
        // Recalculate difference jika ada perubahan actual_quantity
        if ($stockAdjustment->isDirty('actual_quantity') || $stockAdjustment->isDirty('system_quantity')) {
            $stockAdjustment->difference_quantity = $stockAdjustment->actual_quantity - $stockAdjustment->system_quantity;
        }
    }

    /**
     * Generate unique Adjustment Number
     * Format: ADJ-YYYYMM-XXXX (e.g., ADJ-202512-0001)
     */
    private function generateAdjustmentNumber(): string
    {
        $prefix = 'ADJ';
        $yearMonth = now()->format('Ym'); // e.g., 202512

        // Get the latest adjustment number for this month
        $latestAdjustment = StockAdjustment::withTrashed()
            ->where('adjustment_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderByRaw('CAST(SUBSTRING(adjustment_number, -4) AS UNSIGNED) DESC')
            ->first();

        if ($latestAdjustment) {
            // Extract the last 4 digits and increment
            $lastNumber = (int) substr($latestAdjustment->adjustment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            // First adjustment of the month
            $newNumber = 1;
        }

        // Format: ADJ-202512-0001
        return sprintf('%s-%s-%04d', $prefix, $yearMonth, $newNumber);
    }
}
