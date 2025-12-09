# Smart Number Formatting Fix

## Problem

Tabel-tabel menampilkan angka dengan decimal yang berlebihan:

-   **Sebelum:** `10.00` Sak, `100.0000` Kg
-   **Tidak konsisten:** Kadang 2 decimal, kadang 4 decimal
-   **Tidak user-friendly:** Angka bulat seharusnya tanpa decimal

## Solution

Implementasi **smart formatting** dengan helper method `formatQuantity()`:

### Logic

```php
private static function formatQuantity(float $quantity): string
{
    // Format dengan 4 decimal, lalu remove trailing zeros
    return rtrim(rtrim(number_format($quantity, 4, '.', ','), '0'), '.');
}
```

### Hasil Formatting

| Input   | Sebelum   | Sesudah   |
| ------- | --------- | --------- |
| 10      | `10.00`   | `10`      |
| 10.5    | `10.50`   | `10.5`    |
| 10.25   | `10.2500` | `10.25`   |
| 10.1234 | `10.1234` | `10.1234` |
| 0.5     | `0.50`    | `0.5`     |

## Files Modified

### 1. Purchase Order Items (PO)

**File:** `app/Filament/Resources/PurchaseOrders/RelationManagers/ItemsRelationManager.php`

**Changes:**

-   Quantity column: `formatQuantity()` + UoM suffix
-   Example: `10 Sak` instead of `10.00 Sak`

### 2. Good Receipt Items (GR)

**File:** `app/Filament/Resources/GoodReceipts/RelationManagers/ItemsRelationManager.php`

**Changes:**

-   Ordered Quantity: Smart format
-   Received Quantity: Smart format
-   Rejected Quantity: Smart format
-   Accepted Quantity: Smart format
-   All with UoM suffix

### 3. Product Stock

**File:** `app/Filament/Resources/Products/Tables/ProductsTable.php`

**Changes:**

-   Current Stock: `250 Kg` instead of `250.00 Kg`
-   Minimum Stock: `25 Kg` instead of `25.00 Kg`

### 4. BOM (Bill of Materials)

**File:** `app/Filament/Resources/BomHeaders/Tables/BomHeadersTable.php`

**Changes:**

-   Quantity Produced: Smart format with product's stock UoM

## Implementation Pattern

```php
// Before
TextColumn::make('quantity')
    ->numeric()
    ->suffix(fn($record) => ' ' . $record->uom)

// After
TextColumn::make('quantity')
    ->formatStateUsing(fn($state, $record) => static::formatQuantity($state) . ' ' . $record->uom)
    ->alignEnd()
```

## Benefits

✅ **Cleaner Display:**

-   Whole numbers: `10` bukan `10.00`
-   Fractional: `10.5` bukan `10.50`

✅ **Consistent:**

-   Semua tabel pakai format yang sama
-   UoM selalu ditampilkan

✅ **Professional:**

-   Sesuai standar ERP modern
-   User-friendly

## Testing

1. **Purchase Order:**

    - Lihat tabel items di PO
    - Quantity: `10 Sak` (bukan `10.00 Sak`)

2. **Good Receipt:**

    - Lihat Items Received tab
    - Semua quantity clean formatting

3. **Product List:**

    - Current Stock: `100 Kg` (bukan `100.00 Kg`)

4. **BOM List:**
    - Qty Produced: `1 Pcs` (bukan `1.00 Pcs`)

---

**Fixed:** December 9, 2025  
**Issue:** Excessive decimal places in quantity displays  
**Impact:** All quantity fields across the system
