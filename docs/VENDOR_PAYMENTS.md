# Vendor Payments Module Documentation

## Overview

Module **Pembayaran Supplier** (Vendor Payments) melengkapi siklus Purchase-to-Pay dalam sistem ERP bakery. Module ini digunakan untuk mencatat dan mengelola pembayaran kepada supplier/vendor.

## Features

### 1. **Auto-Generated Payment Number**

-   Format: `PAY-YYYYMM-XXXX`
-   Contoh: `PAY-202512-0001`, `PAY-202512-0002`
-   Reset otomatis setiap bulan
-   Unique constraint untuk mencegah duplikasi

### 2. **Payment Methods**

Module mendukung berbagai metode pembayaran:

-   **Cash** (Tunai)
-   **Bank Transfer** (Transfer Bank)
-   **Check** (Cek)
-   **Giro** (Bilyet Giro)
-   **Other** (Lainnya)

### 3. **Payment Status Workflow**

```
Draft → Confirmed → Cancelled
  ↓         ↓           ↓
 Edit    No Edit    No Edit
```

-   **Draft**: Status awal, masih bisa diedit
-   **Confirmed**: Sudah dikonfirmasi, tidak bisa diedit lagi (bisa dibatalkan)
-   **Cancelled**: Pembayaran dibatalkan

### 4. **Link to Purchase Order**

-   Pembayaran bisa dikaitkan dengan PO tertentu (optional)
-   Saat memilih PO, nominal akan otomatis terisi dari total PO
-   Preview total PO untuk referensi

### 5. **Vendor Information**

-   Otomatis menampilkan info vendor saat dipilih:
    -   Nama vendor
    -   Email
    -   Nomor telepon
-   Memudahkan verifikasi data

## Field Descriptions

| Field               | Type    | Required | Description                                          |
| ------------------- | ------- | -------- | ---------------------------------------------------- |
| `payment_number`    | String  | Auto     | Nomor pembayaran (auto-generated)                    |
| `vendor_id`         | FK      | Yes      | Vendor yang dibayar                                  |
| `purchase_order_id` | FK      | No       | PO terkait (optional)                                |
| `payment_date`      | Date    | Yes      | Tanggal pembayaran (max: today)                      |
| `amount`            | Decimal | Yes      | Nominal pembayaran (Rp)                              |
| `payment_method`    | Enum    | Yes      | Metode pembayaran                                    |
| `reference_number`  | String  | No       | Nomor referensi (No. Transfer, No. Cek, dll)         |
| `bank_account`      | String  | No       | Rekening bank (untuk transfer/cek/giro)              |
| `paid_by`           | FK      | Yes      | User yang melakukan pembayaran (default: login user) |
| `status`            | Enum    | Yes      | Status: draft/confirmed/cancelled                    |
| `notes`             | Text    | No       | Catatan tambahan                                     |

## Usage Guide

### Creating a Payment

1. **Navigate** to "Pembelian" → "Pembayaran Supplier"
2. **Click** "New Pembayaran Supplier"
3. **Fill in the form**:
    - Pilih **Vendor** (akan muncul info kontak)
    - Pilih **PO Reference** (optional, nominal akan auto-fill)
    - Atur **Payment Date** (tidak bisa lebih dari hari ini)
    - Masukkan **Amount** (dengan format Rp otomatis)
    - Pilih **Payment Method**
    - Isi **Reference Number** sesuai metode:
        - Cash: No. Kwitansi
        - Bank Transfer: No. Transaksi
        - Check: No. Cek
        - Giro: No. Giro
    - Isi **Bank Account** (muncul otomatis untuk metode bank)
    - Pilih **Paid By** (default: user login)
    - Set **Status** (default: Draft)
    - Tambahkan **Notes** jika perlu
4. **Click** "Create"

### Confirming a Payment

**From List Page:**

1. Find the payment with status "Draft"
2. Click **⚡ Actions** → **Confirm**
3. Confirm the action

**From Edit Page:**

1. Open a draft payment
2. Change status to "Confirmed"
3. Save

⚠️ **Important**: Once confirmed, payment cannot be edited!

### Cancelling a Payment

Only **confirmed** payments can be cancelled:

1. Open the payment
2. Click **Actions** → **Cancel**
3. Confirm the action

The payment will be marked as "Cancelled" but data remains in system.

### Filtering Payments

Available filters:

-   **Status**: Draft / Confirmed / Cancelled
-   **Payment Method**: Cash / Bank Transfer / Check / Giro / Other
-   **Vendor**: Select specific vendor
-   **Date Range**: Payment from - Payment until

### Table Columns

| Column       | Description      | Features                                     |
| ------------ | ---------------- | -------------------------------------------- |
| Payment No.  | PAY-YYYYMM-XXXX  | Copyable, sortable, searchable               |
| Vendor       | Vendor name      | With phone number, sortable                  |
| PO Reference | Linked PO number | Optional, searchable                         |
| Payment Date | Date paid        | Formatted: d M Y                             |
| Amount       | Payment amount   | Smart formatting (no trailing zeros)         |
| Method       | Payment method   | Badge with color coding                      |
| Status       | Current status   | Badge (Draft=⚠️, Confirmed=✅, Cancelled=🚫) |

## Smart Number Formatting

Amounts are displayed with smart formatting:

-   `5000000.00` → `Rp 5,000,000`
-   `5500000.50` → `Rp 5,500,000.5`
-   `5555000.25` → `Rp 5,555,000.25`

No unnecessary trailing zeros!

## Relationships

### From VendorPayment

-   `vendor()` → Belongs to Vendor
-   `purchaseOrder()` → Belongs to PurchaseOrder (nullable)
-   `paidBy()` → Belongs to User
-   `createdBy()` → Belongs to User
-   `updatedBy()` → Belongs to User

### To VendorPayment

-   `Vendor::payments()` → Has many VendorPayment
-   `PurchaseOrder::payments()` → Has many VendorPayment

## Business Rules

1. **Payment Number** is auto-generated and cannot be manually entered
2. **Payment Date** cannot be in the future (max: today)
3. **Draft payments** can be edited and deleted
4. **Confirmed payments** cannot be edited, only cancelled
5. **Cancelled payments** cannot be modified
6. **Bank Account** field is visible only for bank-related methods
7. **Reference Number** label changes based on payment method
8. **Vendor info** is displayed reactively when vendor is selected
9. **PO amount** auto-fills when PO is selected

## Reports & Analytics (Future)

Future enhancements dapat mencakup:

-   Outstanding payments per vendor
-   Payment history by date range
-   Cash flow analysis
-   Vendor payment terms compliance
-   Aging analysis

## Integration Points

### Current Integrations:

-   ✅ Vendors (master data)
-   ✅ Purchase Orders (reference)
-   ✅ Users (paid_by, audit trail)

### Potential Future Integrations:

-   📊 Chart of Accounts (accounting entries)
-   💰 Cash & Bank Management (bank reconciliation)
-   📈 Financial Reports (cash flow statement)
-   🧾 Invoice Matching (3-way matching: PO-GR-Invoice-Payment)

## Example Workflow

### Scenario: Pembayaran Tepung Terigu

1. **Create Payment**

    - Vendor: PT Bogasari Flour Mills
    - PO Reference: PO-202512-0001 (Rp 5,000,000)
    - Amount: Rp 5,000,000 (auto-filled from PO)
    - Method: Bank Transfer
    - Reference: TRF20251209001
    - Bank Account: 1234567890 - Bank Mandiri
    - Status: Draft

2. **Review & Confirm**

    - Check all details
    - Confirm payment
    - Status: Confirmed

3. **Record Complete**
    - Payment number: PAY-202512-0001
    - Vendor can now see payment in their account
    - Finance team has record of outgoing payment

## Troubleshooting

### Issue: Class VendorPaymentForm not found

**Solution**: Run `composer dump-autoload -o` and `php artisan optimize:clear`

### Issue: Payment number not auto-generated

**Solution**: Check VendorPaymentObserver is registered in AppServiceProvider

### Issue: Cannot edit confirmed payment

**Solution**: This is by design. Cancel and create new payment if needed.

### Issue: Smart formatting not working

**Solution**: Check formatQuantity() helper exists in table schema

## Technical Notes

### Observer Pattern

```php
// VendorPaymentObserver.php
public function creating(VendorPayment $payment): void
{
    if (empty($payment->payment_number)) {
        $payment->payment_number = $this->generatePaymentNumber();
    }
}
```

### Status Methods

```php
// Check status
$payment->isDraft();      // bool
$payment->isConfirmed();  // bool
$payment->isCancelled();  // bool

// Change status
$payment->confirm();      // Sets status to confirmed
$payment->cancel();       // Sets status to cancelled
```

### Smart Formatting Helper

```php
private function formatQuantity($quantity): string
{
    $formatted = number_format($quantity, 2, '.', ',');
    return rtrim(rtrim($formatted, '0'), '.');
}
```

## Version History

-   **v1.0** (Dec 2025): Initial release
    -   Basic payment recording
    -   Auto-numbering
    -   Status workflow
    -   PO linking
    -   Smart formatting

---

**Module**: Vendor Payments  
**Version**: 1.0  
**Last Updated**: December 9, 2025  
**Maintainer**: Bakery ERP Team
