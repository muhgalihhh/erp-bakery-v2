# Update Landing Page - Filter Produk Barang Jadi

## Perubahan yang Dilakukan

### Problem:

Landing page menampilkan **semua jenis produk** termasuk bahan baku (raw materials), padahal seharusnya hanya menampilkan **barang jadi (finished goods)** yang siap dijual ke customer.

### Solusi:

Menambahkan filter `type = 'finished'` di controller dan memperbaiki field mapping.

---

## Changes Made

### 1. **LandingController.php** - Method `index()`

**Sebelumnya:**

```php
$products = Product::where('is_active', true)
    ->where('stock', '>', 0)  // Field salah
    ->orderBy('name')
    ->get();
```

**Sekarang:**

```php
$products = Product::where('type', 'finished')  // ✅ Hanya barang jadi
    ->where('is_active', true)
    ->where('is_sellable', true)  // ✅ Produk yang bisa dijual
    ->where('current_stock', '>', 0)  // ✅ Field yang benar
    ->orderBy('name')
    ->get();
```

### 2. **LandingController.php** - Method `products()`

**Filter yang ditambahkan:**

```php
$query = Product::where('type', 'finished')  // ✅ Hanya barang jadi
    ->where('is_sellable', true);  // ✅ Produk yang bisa dijual
```

### 3. **landing/index.blade.php** - Product Display

**Field yang diperbaiki:**

-   ❌ `$product->image` → ✅ `$product->image_url`
-   ❌ `$product->price` → ✅ `$product->selling_price`
-   ❌ `$product->stock` → ✅ `$product->current_stock`

**Fitur yang ditambahkan:**

-   Badge "Stok Terbatas" jika stok <= minimum_stock
-   Tampilan unit satuan (UoM)
-   Info stok tersedia
-   Conditional button "Pesan" (hanya muncul jika stok > 0)

---

## Product Types Explanation

Database memiliki 5 tipe produk:

| Type           | Keterangan              | Ditampilkan di Landing?           |
| -------------- | ----------------------- | --------------------------------- |
| **finished**   | Barang Jadi (siap jual) | ✅ **YA**                         |
| **raw**        | Bahan Baku              | ❌ Tidak                          |
| **wip**        | Work in Progress        | ❌ Tidak                          |
| **service**    | Jasa                    | ❌ Tidak (bisa ditambahkan nanti) |
| **consumable** | Barang Habis Pakai      | ❌ Tidak                          |

### Why Only "Finished" Products?

**Finished Products** adalah produk yang:

-   ✅ Sudah diproduksi/dibuat (dari raw materials)
-   ✅ Siap untuk dijual ke customer
-   ✅ Memiliki harga jual (selling_price)
-   ✅ Bisa ditampilkan di katalog publik

**Raw Materials** contohnya:

-   Tepung terigu
-   Gula
-   Telur
-   Mentega
    → Ini hanya untuk internal produksi, **tidak dijual ke customer**

**Finished Goods** contohnya:

-   Roti Tawar
-   Kue Ulang Tahun
-   Donat
-   Croissant
    → Ini yang **ditampilkan di landing page**

---

## Testing Checklist

### 1. Test Landing Page

```bash
# Akses di browser
http://localhost:8000/
```

**Yang harus dicek:**

-   [ ] Hanya produk dengan type='finished' yang muncul
-   [ ] Harga ditampilkan dengan benar (selling_price)
-   [ ] Stok ditampilkan dengan benar (current_stock)
-   [ ] Badge "Tersedia" atau "Habis" sesuai stok
-   [ ] Badge "Stok Terbatas" muncul jika stok rendah
-   [ ] Button "Pesan" hanya muncul jika stok > 0
-   [ ] WhatsApp link berfungsi dengan benar

### 2. Test Product Catalog Page

```bash
http://localhost:8000/products
```

**Yang harus dicek:**

-   [ ] Hanya finished products yang muncul
-   [ ] Search berfungsi
-   [ ] Filter in_stock berfungsi
-   [ ] Pagination berfungsi

### 3. Test Admin - Create Product

```bash
http://localhost:8000/admin/products/create
```

**Yang harus dicek:**

-   [ ] Field "Type" tersedia dengan pilihan: finished, raw, wip, service, consumable
-   [ ] Field "Is Sellable" untuk menandai produk bisa dijual
-   [ ] Field "Selling Price" untuk harga jual
-   [ ] Upload image ke field "image_url"

---

## Sample SQL untuk Cek Data

### Cek semua produk finished:

```sql
SELECT
    name,
    type,
    is_sellable,
    is_active,
    current_stock,
    selling_price
FROM products
WHERE type = 'finished';
```

### Cek produk yang muncul di landing:

```sql
SELECT
    name,
    type,
    current_stock,
    selling_price
FROM products
WHERE type = 'finished'
  AND is_active = 1
  AND is_sellable = 1
  AND current_stock > 0;
```

---

## Recommendations

### 1. Tambahkan Sample Data

Jika belum ada produk finished, tambahkan melalui admin:

```
Admin → Products → Create New

Contoh:
- Name: Roti Tawar Premium
- Type: finished ✅
- Is Sellable: Yes ✅
- Is Active: Yes ✅
- Current Stock: 50
- Selling Price: 15000
- Upload gambar produk
```

### 2. Upload Product Images

Untuk tampilan yang menarik:

1. Gunakan gambar berkualitas tinggi
2. Ukuran recommended: 800x800px
3. Format: JPG/PNG
4. Compress sebelum upload

### 3. Set Minimum Stock

Agar badge "Stok Terbatas" muncul:

1. Edit product
2. Set "Minimum Stock" (contoh: 10)
3. Ketika current_stock <= 10, badge akan muncul

---

## Next Features (Optional)

### Bisa ditambahkan nanti:

1. **Product Categories**
    - Filter berdasarkan kategori (Roti, Kue, Snack, dll)
2. **Product Reviews**

    - Customer bisa kasih review dan rating

3. **Promo/Discount Display**

    - Tampilkan produk yang sedang promo

4. **New/Best Seller Badge**

    - Badge untuk produk baru atau best seller

5. **Product Variants**
    - Size, rasa, dll (Small/Medium/Large)

---

**Updated:** 7 Januari 2026  
**Status:** ✅ Completed
