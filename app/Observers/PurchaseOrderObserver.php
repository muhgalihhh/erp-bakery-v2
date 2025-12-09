<?php

namespace App\Observers;

use App\Models\PurchaseOrder;

class PurchaseOrderObserver
{
  /**
   * Handle the PurchaseOrder "creating" event.
   */
  public function creating(PurchaseOrder $purchaseOrder): void
  {
    // Generate PO number only if not already set
    if (empty($purchaseOrder->po_number)) {
      $purchaseOrder->po_number = $this->generatePoNumber();
    }
  }

  /**
   * Generate unique PO number
   * Format: PO-YYYYMM-XXXX (e.g., PO-202512-0001)
   */
  private function generatePoNumber(): string
  {
    $prefix = 'PO';
    $yearMonth = now()->format('Ym'); // e.g., 202512

    // Get the latest PO number for this month
    $latestPo = PurchaseOrder::withTrashed()
      ->where('po_number', 'like', "{$prefix}-{$yearMonth}-%")
      ->orderByRaw('CAST(SUBSTRING(po_number, -4) AS UNSIGNED) DESC')
      ->first();

    if ($latestPo) {
      // Extract the last 4 digits and increment
      $lastNumber = (int) substr($latestPo->po_number, -4);
      $newNumber = $lastNumber + 1;
    } else {
      // First PO of the month
      $newNumber = 1;
    }

    // Format: PO-202512-0001
    return sprintf('%s-%s-%04d', $prefix, $yearMonth, $newNumber);
  }
}
