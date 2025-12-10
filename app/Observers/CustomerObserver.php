<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerObserver
{
    /**
     * Handle the Customer "creating" event.
     */
    public function creating(Customer $customer): void
    {
        // Auto-generate customer code jika belum ada
        if (empty($customer->customer_code)) {
            $customer->customer_code = $this->generateCustomerCode();
        }

        // Set created_by
        if (Auth::check() && empty($customer->created_by)) {
            $customer->created_by = Auth::id();
        }
    }

    /**
     * Handle the Customer "updating" event.
     */
    public function updating(Customer $customer): void
    {
        // Set updated_by
        if (Auth::check()) {
            $customer->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }

    /**
     * Generate unique customer code: CUST-XXXX
     */
    private function generateCustomerCode(): string
    {
        $prefix = 'CUST-';

        // Get latest customer code
        $latestCustomer = Customer::withTrashed()
            ->where('customer_code', 'like', $prefix . '%')
            ->orderBy('customer_code', 'desc')
            ->first();

        if ($latestCustomer) {
            // Extract sequence number and increment
            $lastSequence = (int) str_replace($prefix, '', $latestCustomer->customer_code);
            $newSequence = $lastSequence + 1;
        } else {
            // First customer
            $newSequence = 1;
        }

        // Format: CUST-0001, CUST-0002, etc
        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
}

