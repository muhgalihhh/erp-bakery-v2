# Unit of Measure (UoM) Conversion Fix

## Masalah yang Ditemukan

Sebelumnya, sistem **tidak melakukan konversi satuan** saat mengupdate stok dari Good Receipt. Contoh masalah:

### Contoh Kasus: Tepung Terigu

-   **Data Produk:**

    -   Satuan Beli (Purchase UoM): **Sak**
    -   Satuan Stok (Stock UoM): **Kg**
    -   Konversi: **1 Sak = 25 Kg**
    -   Stok Awal: **100 Kg**

-   **Skenario Order:**

    -   PO: Order **10 Sak** tepung
    -   GR: Terima **10 Sak** tepung
    -   Confirm GR

-   **Hasil SEBELUM Fix:**

    -   Stok bertambah: **10 Kg** ❌ (SALAH!)
    -   Stok akhir: **110 Kg**

-   **Hasil SETELAH Fix:**
    -   Stok bertambah: **10 × 25 = 250 Kg** ✅ (BENAR!)
    -   Stok akhir: **350 Kg**

## Root Cause

Method `Product::increaseStock()` dan `decreaseStock()` langsung menambah/mengurangi `current_stock` **tanpa konversi UoM**.

```php
// ❌ SEBELUM (SALAH)
public function increaseStock(float $quantity): void
{
    $this->current_stock += $quantity; // Langsung tambah tanpa konversi!
    $this->save();
}
```

Padahal:

-   **PO dan GR** menggunakan **Purchase UoM** (Sak)
-   **Stock** menggunakan **Stock UoM** (Kg)
-   Perlu konversi: Quantity × `conversion_purchase_to_stock`

## Solusi yang Diterapkan

### 1. Update Model Product

**File:** `app/Models/Product.php`

Tambahkan parameter `$fromUom` untuk menentukan satuan sumber, dan otomatis konversi ke Stock UoM:

```php
// ✅ SETELAH (BENAR)
public function increaseStock(float $quantity, string $fromUom = 'purchase'): void
{
    $quantityInStockUom = $this->convertToStockUom($quantity, $fromUom);
    $this->current_stock += $quantityInStockUom;
    $this->save();
}

public function decreaseStock(float $quantity, string $fromUom = 'usage'): void
{
    $quantityInStockUom = $this->convertToStockUom($quantity, $fromUom);
    $this->current_stock -= $quantityInStockUom;
    $this->save();
}

private function convertToStockUom(float $quantity, string $fromUom): float
{
    return match ($fromUom) {
        'purchase' => $quantity * ($this->conversion_purchase_to_stock ?? 1),
        'usage' => $quantity / ($this->conversion_stock_to_usage ?? 1),
        'stock' => $quantity,
        default => $quantity,
    };
}
```

**Penjelasan:**

-   `fromUom = 'purchase'`: Quantity dari pembelian → konversi dengan `conversion_purchase_to_stock`
-   `fromUom = 'usage'`: Quantity dari pemakaian → konversi dengan `conversion_stock_to_usage`
-   `fromUom = 'stock'`: Sudah dalam satuan stok → tidak perlu konversi

### 2. Update GoodReceipt Model

**File:** `app/Models/GoodReceipt.php`

Update method `confirm()` untuk explicit menentukan `fromUom = 'purchase'`:

```php
// Update inventory stock for each item
foreach ($this->items as $item) {
    $acceptedQty = $item->received_quantity - $item->rejected_quantity;
    if ($acceptedQty > 0) {
        // ✅ Tambahkan parameter 'purchase' untuk auto-konversi
        $item->product->increaseStock($acceptedQty, 'purchase');
    }
}
```

### 3. Improve UX - Display Conversion in Forms

**File:** `app/Filament/Resources/PurchaseOrders/Schemas/PurchaseOrderForm.php`

Tambahkan suffix dan helper text di quantity field:

```php
TextInput::make('quantity')
    ->label('Quantity')
    ->suffix(fn ($get) => Product::find($get('product_id'))?->uom_purchase ?? 'unit')
    ->helperText(fn ($get) => static::getUomConversionInfo($get))
    // Helper text shows: "1 Sak = 25 Kg"
```

**File:** `app/Filament/Resources/GoodReceipts/Schemas/GoodReceiptForm.php`

Tambahkan real-time conversion info:

```php
TextInput::make('received_quantity')
    ->label('Received Qty Now ✍️')
    ->suffix(fn($get) => PurchaseOrderItem::find($get('purchase_order_item_id'))?->product?->uom_purchase ?? 'unit')
    ->helperText(fn ($get) => static::getConversionHelperText($get))
    ->live()
    // Helper text shows: "📦 Will add 250 Kg to stock (1 Sak = 25 Kg)"
```

## Contoh Data Produk dengan UoM

### Tepung Terigu (Raw Material)

```php
'uom_purchase' => 'Sak',
'uom_stock' => 'Kg',
'uom_usage' => 'Gram',
'conversion_purchase_to_stock' => 25,  // 1 Sak = 25 Kg
'conversion_stock_to_usage' => 1000,    // 1 Kg = 1000 Gram
```

**Flow:**

1. **Beli:** 10 Sak → Stok +250 Kg (10 × 25)
2. **Pakai:** 500 Gram → Stok -0.5 Kg (500 / 1000)

### Telur (Raw Material - No Conversion)

```php
'uom_purchase' => 'Kg',
'uom_stock' => 'Kg',
'uom_usage' => 'Kg',
'conversion_purchase_to_stock' => 1,   // 1 Kg = 1 Kg
'conversion_stock_to_usage' => 1,       // 1 Kg = 1 Kg
```

**Flow:**

1. **Beli:** 10 Kg → Stok +10 Kg
2. **Pakai:** 2 Kg → Stok -2 Kg

### Roti Tawar (Finished Good - Pieces)

```php
'uom_purchase' => null,  // Tidak dibeli (produksi sendiri)
'uom_stock' => 'Pcs',
'uom_usage' => 'Pcs',
'conversion_purchase_to_stock' => null,
'conversion_stock_to_usage' => 1,
```

## Testing Guide

### Scenario 1: Purchase with Conversion

1. **Create PO:**

    - Product: Tepung Terigu
    - Quantity: 10 Sak
    - Expected display: "10 Sak" dengan helper "1 Sak = 25 Kg"

2. **Create GR:**

    - Received: 10 Sak
    - Expected helper text: "📦 Will add 250 Kg to stock (1 Sak = 25 Kg)"

3. **Confirm GR:**
    - Check stok tepung
    - Expected: **Stok awal + 250 Kg**

### Scenario 2: Purchase without Conversion

1. **Create PO:**

    - Product: Telur
    - Quantity: 5 Kg

2. **Create GR:**

    - Received: 5 Kg
    - Expected helper text: "📦 Will add 5 Kg to stock (1 Kg = 1 Kg)"

3. **Confirm GR:**
    - Check stok telur
    - Expected: **Stok awal + 5 Kg**

## Impact

✅ **Fixed:**

-   Stock calculation now correctly converts from Purchase UoM to Stock UoM
-   Clear UoM display in all forms (PO, GR)
-   Real-time conversion preview when entering quantities

✅ **User Experience:**

-   Users see conversion info: "1 Sak = 25 Kg"
-   Real-time feedback: "Will add 250 Kg to stock"
-   No confusion about units

✅ **Data Integrity:**

-   Stock levels accurate
-   Financial reports accurate (based on correct stock)
-   Inventory management reliable

## Future Enhancements

📋 **Planned:**

1. Add UoM validation to prevent incorrect manual entries
2. Display stock in multiple UoMs (e.g., "250 Kg (10 Sak)")
3. Add conversion calculator widget
4. Support multiple conversion paths (e.g., Ton → Sak → Kg)

---

**Fixed:** December 9, 2025  
**Issue Reported By:** User (Ifan)  
**Root Cause:** Missing UoM conversion in stock update methods  
**Files Modified:**

-   `app/Models/Product.php`
-   `app/Models/GoodReceipt.php`
-   `app/Filament/Resources/PurchaseOrders/Schemas/PurchaseOrderForm.php`
-   `app/Filament/Resources/GoodReceipts/Schemas/GoodReceiptForm.php`
