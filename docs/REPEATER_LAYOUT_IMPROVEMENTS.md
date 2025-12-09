# Improved Repeater Layout - All Modules

## Overview

Layout repeater untuk Purchase Order Items, Good Receipt Items, dan BOM Ingredients telah diperbaiki agar lebih lebar, nyaman dilihat, dan lebih terorganisir.

## Modules Updated

### 1. 🛒 Purchase Order - Items Tab

### 2. 📦 Good Receipt - Items Received Tab

### 3. 📋 BOM (Resep) - Ingredients Tab

## Perubahan Layout

### 🛒 Purchase Order - Items Tab

#### **Sebelum (Compact - 3 columns)**

```
[Product][Product][Product]
[Qty][Price][Discount]
[Tax][Total][Notes]
```

-   Terlalu padat
-   Sulit dibaca
-   Field tercampur

#### **Sesudah (Wide - 4 columns)**

```
[Product Name - Full Width]
─────────────────────────────────────
[Quantity] [Unit Price] [Discount %] [Tax %]
─────────────────────────────────────
[Item Total - Full Width - Bold Green]
─────────────────────────────────────
[Notes - Full Width]
```

### 📦 Good Receipt - Items Received Tab

#### **Sebelum (Very Compact - 8 columns)**

```
[Prod][Prod][Ord][Alr][Out][Rec][Rej][Acc]
[Notes]
```

-   Sangat sempit
-   Tidak jelas
-   Angka terpotong

#### **Sesudah (Wide - 4 columns)**

```
[Product Name - Full Width]
─────────────────────────────────────
[Ordered] [Already ⚠️] [Outstanding ℹ️] [Accepted ✅]
─────────────────────────────────────
[Received Now ✍️] [Rejected ❌]
─────────────────────────────────────
[Notes - Full Width]
[Rejection Reason - Full Width if rejected]
```

### 📋 BOM/Resep - Ingredients Tab

#### **Sebelum (Compact - 3 columns)**

```
[Product][Product][Product]
[Qty][Waste][Step]
[Notes]
```

-   Field tercampur
-   Kurang jelas
-   Notes terlalu kecil

#### **Sesudah (Wide - 3 columns)**

```
[Ingredient Name - Full Width]
─────────────────────────────────────
[Quantity 📏] [Waste % 🗑️] [Step # 🔢]
─────────────────────────────────────
[Notes - Full Width]
```

## Fitur Baru yang Ditambahkan

### 1. **Collapsible Items** 🔽

-   Repeater items sekarang bisa **diklik untuk expand/collapse**
-   Default state: Collapsed (untuk hemat space)
-   Label menampilkan preview informasi penting
-   Klik untuk expand dan lihat detail lengkap

### 2. **Enhanced Item Labels** 🏷️

**Purchase Order**:

```
📦 Tepung Terigu • Qty: 100 × Rp 15,000 = Rp 1,500,000
```

**Good Receipt**:

```
📦 Tepung Terigu • Ordered: 100 • Outstanding: 50 • Receiving: 50 • ✅ Accepted: 48
```

**BOM/Resep**:

```
📦 Step 1: Tepung Terigu • Qty: 500 gram • Waste: 2%
```

### 3. **Visual Indicators** 🎨

**Color Coding**:

-   🟠 Orange (Warning): Already Received
-   🔵 Blue (Info): Outstanding
-   🟢 Green (Success): Accepted
-   🔴 Red: Rejected (if any)

**Icons**:

-   ✍️ Received Qty Now (editable)
-   ❌ Rejected Qty
-   ✅ Accepted (calculated)
-   ⚠️ Already received
-   ℹ️ Outstanding
-   📏 Quantity
-   🗑️ Waste percentage
-   🔢 Step number

### 4. **Better Spacing** 📐

**Column Distribution**:

-   **Purchase Order**: 4 columns (25% each)

    -   Row 1: Product (100%)
    -   Row 2: Qty | Price | Discount | Tax (25% each)
    -   Row 3: Total (100%)
    -   Row 4: Notes (100%)

-   **Good Receipt**: 4 columns (25% each)

    -   Row 1: Product (100%)
    -   Row 2: Ordered | Already | Outstanding | Accepted (25% each)
    -   Row 3: Received Now | Rejected (50% each)
    -   Row 4: Notes (100%)
    -   Row 5: Rejection Reason if needed (100%)

-   **BOM/Resep**: 3 columns (33% each)
    -   Row 1: Ingredient (100%)
    -   Row 2: Quantity | Waste % | Step Number (33% each)
    -   Row 3: Notes (100%)

### 5. **Live Updates** ⚡

-   Added `->live()` to repeaters
-   Real-time calculation updates
-   Item labels update automatically
-   Better reactive behavior

## Benefits

### ✅ Better Readability

-   Wider fields = easier to read numbers
-   Clear separation between rows
-   Logical grouping (read-only vs editable)

### ✅ Improved UX

-   Collapsible = less scrolling
-   Item labels show key info at a glance
-   Color coding helps identify fields quickly

### ✅ More Professional

-   Organized layout
-   Consistent spacing
-   Visual hierarchy clear

### ✅ Responsive

-   Works well on different screen sizes
-   4-column grid adapts better than 8-column

## Layout Comparison

### Purchase Order Items

| Aspect        | Before     | After                 |
| ------------- | ---------- | --------------------- |
| Columns       | 3          | 4                     |
| Product Field | 1/3 width  | Full width            |
| Total Display | Small text | Bold green, large     |
| Collapsible   | No         | Yes                   |
| Item Label    | Simple     | Detailed with amounts |

### Good Receipt Items

| Aspect            | Before       | After                             |
| ----------------- | ------------ | --------------------------------- |
| Columns           | 8            | 4                                 |
| Product Field     | 2/8 width    | Full width                        |
| Quantities Layout | All in 1 row | 2 rows (read-only, then editable) |
| Color Coding      | Basic        | Enhanced with icons               |
| Collapsible       | No           | Yes                               |
| Item Label        | Text only    | Full summary with all quantities  |

### BOM Ingredients

| Aspect           | Before    | After                        |
| ---------------- | --------- | ---------------------------- |
| Columns          | 3         | 3 (reorganized)              |
| Ingredient Field | 1/3 width | Full width                   |
| Fields Layout    | Mixed     | Grouped logically            |
| Icons            | No        | Yes (📏🗑️🔢)                 |
| Collapsible      | Yes       | Yes                          |
| Item Label       | Simple    | With step number and details |

## Technical Changes

### Files Modified

1. ✅ `app/Filament/Resources/PurchaseOrders/Schemas/PurchaseOrderForm.php`

    - Changed columns from 3 to 4
    - Product field: `columnSpanFull()`
    - Added `->live()` to repeater
    - Enhanced item label with total amount
    - Total display: Added green color and bold

2. ✅ `app/Filament/Resources/GoodReceipts/Schemas/GoodReceiptForm.php`

    - Changed columns from 8 to 4
    - Product field: `columnSpanFull()`
    - Reorganized rows: read-only info, then editable fields
    - Added icons to labels (✍️, ❌)
    - Added `->live()` to repeater
    - Enhanced item label with all key metrics
    - Added `->collapsible()` for better space management

3. ✅ `app/Filament/Resources/BomHeaders/Schemas/BomHeaderForm.php`
    - Kept 3 columns but reorganized layout
    - Ingredient field: `columnSpanFull()`
    - Added icons to labels (📏, 🗑️, 🔢)
    - Enhanced item label with step number
    - Row 2: Quantity | Waste | Step (clearer separation)
    - Row 3: Notes full width
    - Added `->live()` to repeater
    - Simplified helper text

### Key Properties Used

```php
->columns(4)              // 4-column grid
->columnSpanFull()        // Full width
->columnSpan(1)           // Single column (25%)
->collapsible()           // Enable expand/collapse
->live()                  // Real-time updates
->extraAttributes([       // Custom styling
    'class' => 'text-success-600 font-bold'
])
```

## User Guide

### For Purchase Orders

1. **Add Item**: Click "➕ Add Item"
2. **Expand**: Click on item label to expand
3. **Fill**: Product auto-fills price, adjust quantity
4. **Review**: Total updates automatically
5. **Collapse**: Click label again to collapse

### For Good Receipts

1. **Select PO**: Choose approved/partially received PO
2. **Auto-populate**: Items load automatically
3. **Review**: Check "Already Received" and "Outstanding"
4. **Expand Item**: Click to see details
5. **Enter Quantities**: Input received and rejected
6. **See Accepted**: Calculated automatically (green)
7. **Add Notes**: Optional per-item notes

### For BOM/Resep

1. **Add Ingredient**: Click "➕ Tambah Bahan Baku"
2. **Expand**: Click item label to expand
3. **Select**: Choose ingredient from dropdown (full width)
4. **Fill Details**: Quantity 📏, Waste % 🗑️, Step # 🔢
5. **Add Notes**: Optional preparation instructions
6. **Reorder**: Use ↑↓ buttons to change sequence
7. **Collapse**: Click label to minimize

## Testing Checklist

-   [ ] Purchase Order: Create new with 3+ items
-   [ ] Purchase Order: Check collapsible works
-   [ ] Purchase Order: Verify totals update live
-   [ ] Purchase Order: Check item labels show totals
-   [ ] Good Receipt: Create from approved PO
-   [ ] Good Receipt: Items auto-populate correctly
-   [ ] Good Receipt: Already received shows correct numbers
-   [ ] Good Receipt: Outstanding calculated properly
-   [ ] Good Receipt: Accepted updates when changing quantities
-   [ ] Good Receipt: Item labels show all key metrics
-   [ ] Both: Check responsive on different screen sizes
-   [ ] Both: Verify color coding is clear
-   [ ] BOM: Create new recipe with 5+ ingredients
-   [ ] BOM: Check collapsible works
-   [ ] BOM: Verify icons display correctly (📏🗑️🔢)
-   [ ] BOM: Check item labels show step numbers
-   [ ] BOM: Test reorder functionality (↑↓ buttons)
-   [ ] BOM: Verify ingredient dropdown is full width
-   [ ] All: Hard refresh browser (Ctrl+F5) to see changes

## Notes

⚠️ **Cache**: After updating, run `php artisan optimize:clear`
⚠️ **Browser**: Hard refresh (Ctrl+F5) if layout doesn't update
✅ **Backward Compatible**: Existing data still works perfectly
✅ **No Database Changes**: Only UI/layout improvements
