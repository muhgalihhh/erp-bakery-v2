# Navigation Menu - Bakery ERP

## 📋 Struktur Menu (Updated - Bahasa Indonesia)

### 1️⃣ Pengaturan

-   **Pengguna** - Kelola user dan role

### 2️⃣ Akuntansi

-   **Akun Keuangan** - Daftar akun untuk pembukuan (Chart of Accounts)

### 3️⃣ Produksi

1. **Produk** - Master data produk (bahan baku, barang jadi)
2. **Resep Produksi** - Resep/BOM untuk produksi

### 4️⃣ Pembelian

1. **Supplier** - Data supplier/vendor
2. **Order Pembelian** - Purchase Order ke supplier
3. **Terima Barang** - Penerimaan barang dari supplier

---

## 🔄 Alur Kerja Pembelian

```
1. SUPPLIER
   └─ Tambah data supplier (nama, kontak, bank, payment terms)

2. ORDER PEMBELIAN
   └─ Buat PO → Pilih supplier → Tambah item
   └─ Status: Draft → Pending → Approved

3. TERIMA BARANG
   └─ Pilih PO yang sudah approved
   └─ Items otomatis muncul dari PO
   └─ Input jumlah diterima & ditolak
   └─ Confirm → Stock otomatis bertambah ✅
```

---

## 📱 Label Menu yang User-Friendly

| Menu Lama         | Menu Baru           | Penjelasan                          |
| ----------------- | ------------------- | ----------------------------------- |
| Users             | **Pengguna**        | Lebih jelas untuk non-IT            |
| Manajemen User    | **Pengaturan**      | Lebih umum untuk settings           |
| Bagan Akun        | **Akun Keuangan**   | Lebih mudah dipahami                |
| Vendor            | **Supplier**        | Istilah lebih familiar di Indonesia |
| Purchase Orders   | **Order Pembelian** | Bahasa Indonesia murni              |
| Penerimaan Barang | **Terima Barang**   | Lebih sederhana                     |
| BOM Headers       | **Resep Produksi**  | Relatable untuk bakery              |

---

## 🎯 Roles & Access

### Super Admin

-   ✅ Akses semua menu

### Head Baker

-   ✅ Produk
-   ✅ Resep Produksi
-   ✅ Akun Keuangan (view only)

### Inventory Controller

-   ✅ Supplier
-   ✅ Order Pembelian
-   ✅ Terima Barang
-   ✅ Produk (view & update stock)

### Cashier

-   ✅ Produk (view sellable only)
-   ✅ Sales/POS (coming soon)

### Production Staff

-   ✅ Produk (view only)
-   ✅ Resep Produksi (view only)
-   ✅ Production Orders (coming soon)

---

**Last Updated:** December 8, 2025  
**Version:** BakerySys v2 - Navigation Labels Simplified
