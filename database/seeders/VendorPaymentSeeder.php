<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VendorPaymentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get first user for paid_by
    $user = User::first();

    if (!$user) {
      $this->command->warn('No users found. Please seed users first.');
      return;
    }

    // Get vendors
    $vendors = Vendor::all();

    if ($vendors->isEmpty()) {
      $this->command->warn('No vendors found. Please seed vendors first.');
      return;
    }

    // Get some purchase orders
    $purchaseOrders = PurchaseOrder::whereIn('status', ['approved', 'completed'])->get();

    $payments = [];

    // Payment 1: Cash payment to first vendor (confirmed)
    $payments[] = [
      'vendor_id' => $vendors[0]->id,
      'purchase_order_id' => $purchaseOrders->isNotEmpty() ? $purchaseOrders[0]->id : null,
      'paid_by' => $user->id,
      'payment_date' => Carbon::now()->subDays(10),
      'amount' => 5000000.00,
      'payment_method' => VendorPayment::METHOD_CASH,
      'reference_number' => 'CASH-001',
      'bank_account' => null,
      'status' => VendorPayment::STATUS_CONFIRMED,
      'notes' => 'Pembayaran tunai untuk order tepung terigu',
      'created_by' => $user->id,
      'updated_by' => $user->id,
      'created_at' => Carbon::now()->subDays(10),
      'updated_at' => Carbon::now()->subDays(10),
    ];

    // Payment 2: Bank transfer to second vendor (confirmed)
    if ($vendors->count() > 1) {
      $payments[] = [
        'vendor_id' => $vendors[1]->id,
        'purchase_order_id' => $purchaseOrders->count() > 1 ? $purchaseOrders[1]->id : null,
        'paid_by' => $user->id,
        'payment_date' => Carbon::now()->subDays(7),
        'amount' => 15000000.00,
        'payment_method' => VendorPayment::METHOD_BANK_TRANSFER,
        'reference_number' => 'TRF20251201001',
        'bank_account' => '1234567890 - Bank Mandiri',
        'status' => VendorPayment::STATUS_CONFIRMED,
        'notes' => 'Transfer bank untuk pembayaran supplier',
        'created_by' => $user->id,
        'updated_by' => $user->id,
        'created_at' => Carbon::now()->subDays(7),
        'updated_at' => Carbon::now()->subDays(7),
      ];
    }

    // Payment 3: Check payment (draft)
    if ($vendors->count() > 2) {
      $payments[] = [
        'vendor_id' => $vendors[2]->id,
        'purchase_order_id' => $purchaseOrders->count() > 2 ? $purchaseOrders[2]->id : null,
        'paid_by' => $user->id,
        'payment_date' => Carbon::now(),
        'amount' => 8500000.00,
        'payment_method' => VendorPayment::METHOD_CHECK,
        'reference_number' => 'CHK-20251208-001',
        'bank_account' => 'Bank BCA',
        'status' => VendorPayment::STATUS_DRAFT,
        'notes' => 'Pembayaran via cek - menunggu konfirmasi',
        'created_by' => $user->id,
        'updated_by' => $user->id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ];
    }

    // Payment 4: Giro payment (confirmed)
    if ($vendors->count() > 0) {
      $payments[] = [
        'vendor_id' => $vendors[0]->id,
        'purchase_order_id' => null, // Not linked to specific PO
        'paid_by' => $user->id,
        'payment_date' => Carbon::now()->subDays(3),
        'amount' => 12000000.00,
        'payment_method' => VendorPayment::METHOD_GIRO,
        'reference_number' => 'GIRO-20251205-002',
        'bank_account' => 'Bank BNI',
        'status' => VendorPayment::STATUS_CONFIRMED,
        'notes' => 'Pembayaran dengan giro - jatuh tempo 30 hari',
        'created_by' => $user->id,
        'updated_by' => $user->id,
        'created_at' => Carbon::now()->subDays(3),
        'updated_at' => Carbon::now()->subDays(3),
      ];
    }

    // Payment 5: Another bank transfer (draft)
    if ($vendors->count() > 1) {
      $payments[] = [
        'vendor_id' => $vendors[1]->id,
        'purchase_order_id' => null,
        'paid_by' => $user->id,
        'payment_date' => Carbon::now()->addDays(2),
        'amount' => 20000000.00,
        'payment_method' => VendorPayment::METHOD_BANK_TRANSFER,
        'reference_number' => null,
        'bank_account' => '9876543210 - Bank BCA',
        'status' => VendorPayment::STATUS_DRAFT,
        'notes' => 'Jadwal pembayaran untuk minggu depan',
        'created_by' => $user->id,
        'updated_by' => $user->id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ];
    }

    // Payment 6: Cancelled payment
    if ($vendors->count() > 2) {
      $payments[] = [
        'vendor_id' => $vendors[2]->id,
        'purchase_order_id' => null,
        'paid_by' => $user->id,
        'payment_date' => Carbon::now()->subDays(15),
        'amount' => 7500000.00,
        'payment_method' => VendorPayment::METHOD_CASH,
        'reference_number' => 'CASH-CANCELLED',
        'bank_account' => null,
        'status' => VendorPayment::STATUS_CANCELLED,
        'notes' => 'Pembayaran dibatalkan karena perubahan order',
        'created_by' => $user->id,
        'updated_by' => $user->id,
        'created_at' => Carbon::now()->subDays(15),
        'updated_at' => Carbon::now()->subDays(14),
      ];
    }

    // Insert all payments (payment_number will be auto-generated by observer)
    foreach ($payments as $payment) {
      VendorPayment::create($payment);
    }

    $this->command->info('Vendor payments seeded successfully! Total: ' . count($payments));
  }
}
