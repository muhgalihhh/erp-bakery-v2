<?php

namespace App\Observers;

use App\Models\GoodReceipt;

class GoodReceiptObserver
{
  /**
   * Handle the GoodReceipt "creating" event.
   */
  public function creating(GoodReceipt $goodReceipt): void
  {
    // Generate receipt number only if not already set
    if (empty($goodReceipt->receipt_number)) {
      $goodReceipt->receipt_number = $this->generateReceiptNumber();
    }
  }

  /**
   * Generate unique Receipt number
   * Format: GR-YYYYMM-XXXX (e.g., GR-202512-0001)
   */
  private function generateReceiptNumber(): string
  {
    $prefix = 'GR';
    $yearMonth = now()->format('Ym'); // e.g., 202512

    // Get the latest receipt number for this month
    $latestReceipt = GoodReceipt::withTrashed()
      ->where('receipt_number', 'like', "{$prefix}-{$yearMonth}-%")
      ->orderByRaw('CAST(SUBSTRING(receipt_number, -4) AS UNSIGNED) DESC')
      ->first();

    if ($latestReceipt) {
      // Extract the last 4 digits and increment
      $lastNumber = (int) substr($latestReceipt->receipt_number, -4);
      $newNumber = $lastNumber + 1;
    } else {
      // First receipt of the month
      $newNumber = 1;
    }

    // Format: GR-202512-0001
    return sprintf('%s-%s-%04d', $prefix, $yearMonth, $newNumber);
  }
}
