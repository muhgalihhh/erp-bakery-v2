# HPP (Harga Pokok Penjualan) = 0/Unit - Analisis & Solusi

## 🔍 Masalah

Saat melakukan transaksi produksi atau penjualan, HPP (Harga Pokok Penjualan / Cost Price) tercatat sebagai **0/unit**, padahal seharusnya ada nilai cost-nya.

## 📊 Penyebab Masalah

### 1. **Produk Tanpa Standard Cost**

Produk yang baru dibuat mungkin tidak memiliki nilai `standard_cost` di database.

```sql
-- Cek produk tanpa standard_cost
SELECT id, sku, name, standard_cost, type
FROM products
WHERE (standard_cost IS NULL OR standard_cost = 0)
AND is_sellable = 1;
```

### 2. **Produk Finished Goods Belum Dihitung HPP dari BOM**

Untuk produk hasil produksi (finished goods), HPP harus dihitung berdasarkan:

-   **Bill of Materials (BOM)** - Resep/formula pembuatan
-   **Standard Cost Material** - Harga bahan baku yang digunakan
-   **Overhead** - Biaya produksi lainnya

## ✅ Solusi

### Solusi 1: Update Manual Standard Cost di Database

Jalankan query untuk mengisi standard_cost produk yang kosong:

```sql
-- Update produk raw material berdasarkan purchase_price
UPDATE products
SET standard_cost = purchase_price
WHERE type = 'raw_material'
AND (standard_cost IS NULL OR standard_cost = 0);

-- Update produk finished goods dengan estimasi 70% dari selling price
UPDATE products
SET standard_cost = selling_price * 0.7
WHERE type = 'finished_goods'
AND (standard_cost IS NULL OR standard_cost = 0);
```

### Solusi 2: Auto-Calculate HPP dari BOM (Recommended)

Saya akan membuat sistem yang otomatis menghitung HPP untuk produk finished goods berdasarkan BOM.

#### A. Tambah Method di Model Product

File: `app/Models/Product.php`

```php
/**
 * Calculate standard cost from BOM
 */
public function calculateStandardCostFromBom(): float
{
    // Cari BOM aktif untuk produk ini
    $bom = $this->hasMany(BomHeader::class, 'product_id')
        ->where('is_active', true)
        ->first();

    if (!$bom) {
        return 0;
    }

    $totalCost = 0;

    // Hitung cost dari setiap material/item di BOM
    foreach ($bom->items as $item) {
        $material = $item->material; // product yang jadi material

        if (!$material) {
            continue;
        }

        // Cost per unit material * quantity yang dibutuhkan
        $materialCost = ($material->standard_cost ?? 0) * $item->quantity;
        $totalCost += $materialCost;
    }

    // Tambahkan overhead jika ada
    if ($bom->overhead_percentage > 0) {
        $overhead = $totalCost * ($bom->overhead_percentage / 100);
        $totalCost += $overhead;
    }

    // Bagi dengan output quantity
    if ($bom->output_quantity > 0) {
        return $totalCost / $bom->output_quantity;
    }

    return $totalCost;
}

/**
 * Update standard cost from BOM
 */
public function updateStandardCostFromBom(): bool
{
    $calculatedCost = $this->calculateStandardCostFromBom();

    if ($calculatedCost > 0) {
        $this->update(['standard_cost' => $calculatedCost]);
        return true;
    }

    return false;
}
```

#### B. Tambah Observer untuk Auto-Update

File: `app/Observers/BomHeaderObserver.php`

```php
<?php

namespace App\Observers;

use App\Models\BomHeader;

class BomHeaderObserver
{
    /**
     * Handle the BomHeader "created" event.
     */
    public function created(BomHeader $bomHeader): void
    {
        $this->updateProductStandardCost($bomHeader);
    }

    /**
     * Handle the BomHeader "updated" event.
     */
    public function updated(BomHeader $bomHeader): void
    {
        $this->updateProductStandardCost($bomHeader);
    }

    /**
     * Update product standard cost when BOM changes
     */
    protected function updateProductStandardCost(BomHeader $bomHeader): void
    {
        if ($bomHeader->is_active && $bomHeader->product) {
            $bomHeader->product->updateStandardCostFromBom();
        }
    }
}
```

#### C. Register Observer

File: `app/Providers/AppServiceProvider.php`

```php
use App\Models\BomHeader;
use App\Observers\BomHeaderObserver;

public function boot(): void
{
    BomHeader::observe(BomHeaderObserver::class);
}
```

### Solusi 3: Validasi di Form Product

Tambahkan validasi agar user wajib mengisi standard_cost saat membuat produk.

File: `app/Filament/Resources/.../ProductForm.php`

```php
TextInput::make('standard_cost')
    ->label('Standard Cost (HPP)')
    ->numeric()
    ->prefix('Rp')
    ->required() // Wajib diisi
    ->minValue(0)
    ->helperText('Untuk finished goods, akan auto-calculate dari BOM')
```

### Solusi 4: Command untuk Recalculate All Products

Buat Artisan command untuk recalculate semua HPP produk:

```bash
php artisan make:command RecalculateProductCost
```

File: `app/Console/Commands/RecalculateProductCost.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class RecalculateProductCost extends Command
{
    protected $signature = 'product:recalculate-cost {--type=finished_goods}';
    protected $description = 'Recalculate standard cost for products from BOM';

    public function handle()
    {
        $type = $this->option('type');

        $products = Product::where('type', $type)
            ->where('is_active', true)
            ->get();

        $this->info("Recalculating cost for {$products->count()} products...");

        $updated = 0;
        foreach ($products as $product) {
            if ($product->updateStandardCostFromBom()) {
                $this->line("✓ {$product->sku} - {$product->name}: Rp " . number_format($product->standard_cost, 0, ',', '.'));
                $updated++;
            }
        }

        $this->info("\nSuccessfully updated {$updated} products!");
    }
}
```

Jalankan dengan:

```bash
php artisan product:recalculate-cost
```

## 🎯 Implementasi Langkah demi Langkah

### Step 1: Quick Fix - Update Manual

```bash
php artisan tinker
```

```php
// Update semua raw material
DB::table('products')
    ->where('type', 'raw_material')
    ->whereNull('standard_cost')
    ->update(['standard_cost' => DB::raw('purchase_price')]);

// Atau untuk produk spesifik
$product = Product::find(1);
$product->update(['standard_cost' => 15000]);
```

### Step 2: Tambah Method di Product Model

1. Buka `app/Models/Product.php`
2. Tambahkan method `calculateStandardCostFromBom()` dan `updateStandardCostFromBom()`

### Step 3: Buat Observer (Optional tapi Recommended)

1. Buat BomHeaderObserver
2. Register di AppServiceProvider
3. Setiap kali BOM diupdate, HPP akan auto-update

### Step 4: Buat Command untuk Bulk Update

1. Buat RecalculateProductCost command
2. Jalankan untuk update semua produk sekaligus

## 📈 Monitoring & Prevention

### 1. Tambahkan Validation

Pastikan setiap produk baru WAJIB memiliki standard_cost:

```php
// Di ProductResource form
TextInput::make('standard_cost')
    ->required()
    ->rules(['required', 'numeric', 'min:0'])
```

### 2. Dashboard Alert

Tambahkan widget di dashboard untuk monitor produk tanpa HPP:

```php
// ProductsWithoutCostWidget
SELECT COUNT(*) FROM products
WHERE (standard_cost IS NULL OR standard_cost = 0)
AND is_sellable = 1
```

### 3. Report Regular

Buat laporan mingguan produk dengan HPP = 0:

```bash
php artisan product:check-cost --notify
```

## 🔧 Troubleshooting

### Q: HPP masih 0 setelah update?

**A:** Cek apakah BOM sudah dibuat dan diset `is_active = true`

### Q: Bagaimana untuk produk yang dibeli (bukan diproduksi)?

**A:** Untuk raw material dan purchased goods, gunakan `purchase_price` sebagai standard_cost

### Q: HPP berbeda dengan actual cost produksi?

**A:** Standard cost adalah estimasi. Untuk actual cost, lihat di Production Order atau Stock Movement

### Q: Bagaimana menghitung overhead?

**A:** Tambahkan `overhead_percentage` di BOM Header (misalnya 15% untuk listrik, tenaga kerja, dll)

## 📝 Catatan Penting

1. **Standard Cost ≠ Purchase Price**

    - Purchase Price: Harga beli dari vendor
    - Standard Cost: HPP yang digunakan untuk perhitungan profit

2. **Standard Cost ≠ Actual Cost**

    - Standard Cost: Harga standar/estimasi
    - Actual Cost: Harga real saat produksi (bisa berbeda)

3. **Update Regular**
    - Review standard cost setiap bulan
    - Adjust berdasarkan actual cost rata-rata
    - Perhatikan perubahan harga bahan baku

## 🎓 Best Practices

1. ✅ Set standard cost saat membuat produk baru
2. ✅ Review dan update standard cost secara berkala
3. ✅ Gunakan BOM untuk auto-calculate finished goods
4. ✅ Track variance antara standard vs actual cost
5. ✅ Include overhead dalam perhitungan
6. ✅ Dokumentasikan perubahan standard cost

---

**Status**: Solusi siap diimplementasikan
**Priority**: HIGH - Impact langsung ke profit calculation
**Effort**: Medium - Butuh beberapa perubahan di model dan observer
