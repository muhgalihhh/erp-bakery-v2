<?php

namespace App\Observers;

use App\Models\VendorPayment;
use Carbon\Carbon;

class VendorPaymentObserver
{
    /**
     * Handle the VendorPayment "creating" event.
     * Generate payment number: PAY-YYYYMM-XXXX
     */
    public function creating(VendorPayment $vendorPayment): void
    {
        if (empty($vendorPayment->payment_number)) {
            $vendorPayment->payment_number = $this->generatePaymentNumber();
        }
    }

    /**
     * Generate unique payment number
     * Format: PAY-YYYYMM-XXXX (e.g., PAY-202512-0001)
     */
    private function generatePaymentNumber(): string
    {
        $yearMonth = Carbon::now()->format('Ym'); // e.g., 202512
        $prefix = "PAY-{$yearMonth}-";

        // Find latest payment number for current month (including soft-deleted)
        $latestPayment = VendorPayment::withTrashed()
            ->where('payment_number', 'like', $prefix . '%')
            ->orderBy('payment_number', 'desc')
            ->first();

        if ($latestPayment) {
            // Extract sequence number and increment
            $lastSequence = (int) substr($latestPayment->payment_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            // First payment of the month
            $newSequence = 1;
        }

        // Format: PAY-202512-0001
        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}
