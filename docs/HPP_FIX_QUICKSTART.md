# Quick Guide: Fix HPP 0/Unit di BakerySys

## 🎯 Masalah Terselesaikan!

Saya telah menambahkan sistem otomatis untuk menghitung dan mengisi HPP (Harga Pokok Penjualan) yang kosong.

## ✅ Yang Sudah Dilakukan

### 1. **Tambah Method di Product Model** ✓

File: `app/Models/Product.php`

Menambahkan 4 method baru:

-   `calculateStandardCostFromBom()` - Hitung HPP dari BOM
-   `updateStandardCostFromBom()` - Update HPP ke database
-   `ensureStandardCost()` - Pastikan produk punya HPP
-   `getActiveBom()` - Ambil BOM aktif produk

### 2. **Auto-Calculate di POS** ✓

File: `app/Filament/Resources/PointOfSaleResource/Pages/PointOfSalePage.php`

Sekarang saat produk ditambahkan ke keranjang POS, sistem otomatis:

1. Cek apakah produk punya `standard_cost`
2. Jika tidak ada atau 0, auto-calculate berdasarkan:
    - **Finished Goods**: Hitung dari BOM
    - **Raw Material**: Gunakan purchase_price
    - **Packaging**: Gunakan purchase_price
3. Simpan ke database untuk digunakan selanjutnya

### 3. **Command untuk Bulk Update** ✓

File: `app/Console/Commands/RecalculateProductCost.php`

Buat command untuk recalculate semua produk sekaligus.

## 🚀 Cara Menggunakan

### Option 1: Otomatis Saat Transaksi POS

Tidak perlu lakukan apa-apa!

Saat Anda menambahkan produk ke keranjang POS, sistem akan:

-   Auto-calculate HPP jika belum ada
-   Menyimpan ke database
-   Menggunakan HPP untuk hitung profit

### Option 2: Manual Recalculate Semua Produk

Jalankan command ini untuk update semua produk yang HPP-nya 0:

```bash
php artisan product:recalculate-cost
```

Output contoh:

```
🔄 Starting product cost recalculation...

Found 15 products to recalculate.

  ✓ RTI-001 - Roti Tawar: Rp 0 → Rp 12,500
  ✓ KUE-002 - Kue Bolu: Rp 0 → Rp 8,300
  ✓ PKG-001 - Plastik Kemasan: Rp 0 → Rp 500

✅ Recalculation completed!

┌──────────────────────┬───────┐
│ Status               │ Count │
├──────────────────────┼───────┤
│ Total products       │ 15    │
│ Updated              │ 13    │
│ Skipped (no change)  │ 2     │
└──────────────────────┴───────┘
```

### Option 3: Recalculate by Type

Hanya update finished goods:

```bash
php artisan product:recalculate-cost --type=finished_goods
```

Hanya update raw materials:

```bash
php artisan product:recalculate-cost --type=raw_material
```

### Option 4: Recalculate All (termasuk yang sudah ada HPP)

```bash
php artisan product:recalculate-cost --all
```

## 📊 Logika Perhitungan HPP

### 1. Finished Goods (Produk Jadi)

```
HPP = Total Cost Bahan Baku dari BOM / Quantity Produced

Contoh:
BOM Roti Tawar:
- Tepung: 1 kg × Rp 10,000 = Rp 10,000
- Gula: 0.2 kg × Rp 12,000 = Rp 2,400
- Ragi: 0.05 kg × Rp 20,000 = Rp 1,000
Total Cost = Rp 13,400

Jika 1 BOM menghasilkan 10 pcs roti:
HPP per pcs = Rp 13,400 / 10 = Rp 1,340
```

**Jika Tidak Ada BOM:**

-   Fallback: 60% dari selling price
-   Contoh: Selling Rp 5,000 → HPP = Rp 3,000

### 2. Raw Material (Bahan Baku)

```
HPP = Purchase Price

Contoh:
Tepung Terigu dibeli Rp 10,000/kg
→ Standard Cost = Rp 10,000
```

### 3. Packaging (Kemasan)

```
HPP = Purchase Price

Contoh:
Plastik Kemasan dibeli Rp 500/pcs
→ Standard Cost = Rp 500
```

## 🔍 Cara Cek HPP Produk

### Via Database

```sql
SELECT
    sku,
    name,
    type,
    purchase_price,
    selling_price,
    standard_cost,
    CASE
        WHEN standard_cost IS NULL OR standard_cost = 0 THEN '❌ Kosong'
        ELSE '✅ Ada'
    END as status_hpp
FROM products
WHERE is_active = 1
ORDER BY type, sku;
```

### Via Tinker

```bash
php artisan tinker
```

```php
// Cek produk tanpa HPP
Product::whereNull('standard_cost')
    ->orWhere('standard_cost', 0)
    ->get(['id', 'sku', 'name', 'type', 'standard_cost']);

// Cek HPP specific product
$product = Product::where('sku', 'RTI-001')->first();
echo "SKU: {$product->sku}\n";
echo "Nama: {$product->name}\n";
echo "HPP: Rp " . number_format($product->standard_cost, 0, ',', '.') . "\n";

// Hitung ulang HPP dari BOM
$product->ensureStandardCost();
$product->refresh();
echo "HPP Baru: Rp " . number_format($product->standard_cost, 0, ',', '.') . "\n";
```

## 📝 Tips & Best Practices

### 1. Update HPP Secara Berkala

Jalankan command ini setiap:

-   Membuat produk baru
-   Update harga bahan baku
-   Mengubah BOM produk
-   Akhir bulan (monthly review)

```bash
# Tambahkan ke cron job
0 0 1 * * cd /path/to/bakery-erp && php artisan product:recalculate-cost
```

### 2. Untuk Produk Baru

Pastikan saat membuat produk baru:

-   **Finished Goods**: Buat BOM terlebih dahulu, lalu jalankan recalculate
-   **Raw Material**: Isi purchase_price
-   **Packaging**: Isi purchase_price

### 3. Monitor Produk Tanpa HPP

Buat widget dashboard:

```php
// Count products without standard cost
$countWithoutCost = Product::where('is_sellable', true)
    ->where(function($q) {
        $q->whereNull('standard_cost')
          ->orWhere('standard_cost', 0);
    })
    ->count();

if ($countWithoutCost > 0) {
    Notification::make()
        ->warning()
        ->title("$countWithoutCost produk belum punya HPP")
        ->body('Jalankan: php artisan product:recalculate-cost')
        ->send();
}
```

### 4. Validasi Saat Input

Tambahkan validasi di form product:

```php
TextInput::make('standard_cost')
    ->required()
    ->minValue(0)
    ->helperText(function ($record) {
        if ($record && $record->type === 'finished_goods') {
            $bom = $record->getActiveBom();
            if ($bom) {
                $calculated = $bom->calculateCost();
                return "HPP dari BOM: Rp " . number_format($calculated, 0, ',', '.');
            }
        }
        return 'Isi manual atau akan auto-calculate';
    })
```

## 🐛 Troubleshooting

### Q: Setelah recalculate, HPP masih 0?

**A:** Kemungkinan:

1. Produk belum punya BOM (untuk finished goods)
2. BOM belum diset is_active = true
3. Material di BOM juga belum punya standard_cost/purchase_price

**Solusi:**

```bash
# Cek BOM produk
php artisan tinker
```

```php
$product = Product::find(1);
$bom = $product->getActiveBom();
if (!$bom) {
    echo "❌ Produk belum punya BOM aktif\n";
} else {
    echo "✅ BOM: {$bom->bom_code}\n";
    foreach ($bom->items as $item) {
        echo "  - {$item->product->sku}: Rp " . number_format($item->product->standard_cost ?? 0, 0) . "\n";
    }
}
```

### Q: HPP calculated terlalu tinggi/rendah?

**A:** Review BOM:

1. Pastikan quantity bahan baku sudah benar
2. Cek waste percentage
3. Update purchase_price bahan baku
4. Recalculate lagi

### Q: Bagaimana dengan overhead (listrik, tenaga kerja, dll)?

**A:** Overhead belum included dalam perhitungan otomatis.
Manual adjustment:

```php
$product = Product::find(1);
$costFromBom = $product->calculateStandardCostFromBom();
$overhead = $costFromBom * 0.15; // 15% overhead
$product->update(['standard_cost' => $costFromBom + $overhead]);
```

## 📚 Dokumentasi Lengkap

Lihat dokumentasi detail di:

-   `docs/HPP_ZERO_SOLUTION.md` - Analisis lengkap masalah & solusi
-   `docs/POS_MODERN_INTERFACE.md` - Dokumentasi interface POS

## ✨ Hasil Akhir

Sekarang setiap transaksi POS akan:
✅ Punya HPP yang benar  
✅ Menghitung profit akurat  
✅ Tracking cost dengan tepat  
✅ Report keuangan yang valid

---

**Next Steps:**

1. Jalankan `php artisan product:recalculate-cost` sekarang
2. Review hasil di database
3. Setup cron job untuk auto-update monthly
4. Add validation di product form

Selamat! HPP sekarang sudah otomatis terisi dengan benar! 🎉
