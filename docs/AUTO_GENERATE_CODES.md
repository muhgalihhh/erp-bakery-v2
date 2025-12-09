# Auto-Generated Unique Codes

## Overview

Sistem sekarang secara otomatis menggenerate kode unik untuk Purchase Order dan Good Receipt tanpa perlu input manual.

## Format Kode

### Purchase Order Number (PO Number)

-   **Format**: `PO-YYYYMM-XXXX`
-   **Contoh**: `PO-202512-0001`, `PO-202512-0002`, `PO-202601-0001`
-   **Penjelasan**:
    -   `PO` = Prefix untuk Purchase Order
    -   `YYYYMM` = Tahun dan Bulan (contoh: 202512 untuk Desember 2025)
    -   `XXXX` = Nomor urut 4 digit (auto-increment per bulan)
-   **Status**: ✅ AUTO-GENERATED

### Good Receipt Number (Receipt Number)

-   **Format**: `GR-YYYYMM-XXXX`
-   **Contoh**: `GR-202512-0001`, `GR-202512-0002`, `GR-202601-0001`
-   **Penjelasan**:
    -   `GR` = Prefix untuk Good Receipt
    -   `YYYYMM` = Tahun dan Bulan
    -   `XXXX` = Nomor urut 4 digit (auto-increment per bulan)
-   **Status**: ✅ AUTO-GENERATED

### Delivery Note Number

-   **Format**: BEBAS (dari vendor)
-   **Contoh**: `SJ-VENDOR-12345`, `DN/2025/001`, dll
-   **Penjelasan**:
    -   Nomor surat jalan dari vendor/supplier
    -   BUKAN nomor yang kita generate
    -   Diinput manual sesuai dokumen dari vendor
-   **Status**: ⚠️ MANUAL INPUT (Optional)

## Cara Kerja

### 1. Observer Pattern

Sistem menggunakan Laravel Observer yang otomatis trigger saat membuat record baru:

-   `PurchaseOrderObserver` → Generate `po_number`
-   `GoodReceiptObserver` → Generate `receipt_number`

### 2. Auto-Increment Logic

-   Sistem mencari nomor terakhir untuk bulan yang sama
-   Extract 4 digit terakhir dan tambahkan 1
-   Jika bulan baru, mulai dari 0001
-   Format dengan `sprintf()` untuk padding 4 digit

### 3. Registered di AppServiceProvider

```php
PurchaseOrder::observe(PurchaseOrderObserver::class);
GoodReceipt::observe(GoodReceiptObserver::class);
```

## User Interface

### Form Create (Buat Baru)

-   Field `po_number` / `receipt_number` **TIDAK DITAMPILKAN**
-   User hanya input data lain (vendor, tanggal, items, dll)
-   Nomor akan di-generate otomatis saat save

### Form Edit

-   Field `po_number` / `receipt_number` **DITAMPILKAN sebagai Placeholder**
-   Read-only (tidak bisa diubah)
-   Menampilkan nomor yang sudah digenerate
-   Helper text: "📝 Generated automatically: PO-YYYYMM-XXXX"

### Table List

-   Kolom `po_number` / `receipt_number` tetap ditampilkan
-   Searchable, sortable, copyable
-   Icon document, bold, primary color

## Keuntungan

✅ **No Manual Input**: User tidak perlu memikirkan format nomor
✅ **Consistent Format**: Semua nomor pasti mengikuti format yang sama
✅ **Auto-Increment**: Tidak ada duplikat, urut otomatis
✅ **Monthly Reset**: Nomor reset setiap bulan baru untuk tracking lebih mudah
✅ **Unique Guarantee**: Kombinasi YYYYMM-XXXX memastikan keunikan
✅ **Delivery Note Flexibility**: Opsional, bisa kosong, input sesuai vendor

## Field Behavior Summary

| Field                  | Type   | Required | Auto-Generate | User Input        |
| ---------------------- | ------ | -------- | ------------- | ----------------- |
| `po_number`            | String | Yes      | ✅ Yes        | ❌ No (read-only) |
| `receipt_number`       | String | Yes      | ✅ Yes        | ❌ No (read-only) |
| `delivery_note_number` | String | No       | ❌ No         | ✅ Yes (optional) |

## Testing

### Test Purchase Order

1. Buka menu Purchase Orders → Create
2. Isi Vendor, tanggal, items
3. **JANGAN isi PO Number** (tidak ada fieldnya)
4. Save
5. Cek: Nomor otomatis muncul (contoh: PO-202512-0001)

### Test Good Receipt

1. Buka menu Good Receipts → Create
2. Pilih PO yang sudah approved
3. Isi tanggal penerimaan, items
4. **JANGAN isi Receipt Number** (tidak ada fieldnya)
5. Save
6. Cek: Nomor otomatis muncul (contoh: GR-202512-0001)

### Test Edit

1. Edit PO atau GR yang sudah dibuat
2. Lihat field nomor: Ditampilkan tapi read-only
3. Tidak bisa diubah, hanya informasi

## Technical Files

### Observer Files

-   `app/Observers/PurchaseOrderObserver.php`
-   `app/Observers/GoodReceiptObserver.php`

### Registration

-   `app/Providers/AppServiceProvider.php`

### Form Updates

-   `app/Filament/Resources/PurchaseOrders/Schemas/PurchaseOrderForm.php`
-   `app/Filament/Resources/GoodReceipts/Schemas/GoodReceiptForm.php`

### Migration

-   `database/migrations/2025_12_08_131438_make_unique_codes_nullable_in_purchase_orders_and_good_receipts.php`

## Notes

⚠️ **Jangan ubah field nullable**: po_number dan receipt_number harus nullable di database
⚠️ **Jangan hapus Observer**: Tanpa observer, nomor tidak akan tergenerate
⚠️ **Cache Clear**: Setelah update observer, jalankan `php artisan optimize:clear`
