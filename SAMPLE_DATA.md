# Sample Data - BakerySys v2

## 📦 Sample Data yang Tersedia

### 🏢 Vendors (5 Suppliers)

| Kode    | Nama                      | Kategori      | Payment Terms | Contact               |
| ------- | ------------------------- | ------------- | ------------- | --------------------- |
| SUP-001 | PT Bogasari Flour Mills   | Tepung Terigu | NET 30        | sales@bogasari.co.id  |
| SUP-002 | CV Mitra Telur Sejahtera  | Telur Ayam    | NET 14        | order@mitratelur.com  |
| SUP-003 | PT Frisian Flag Indonesia | Produk Dairy  | NET 45        | b2b@frisianflag.com   |
| SUP-004 | Toko Bahan Kue Sinar Jaya | Bahan Kue     | NET 7         | sinarjaya@gmail.com   |
| SUP-005 | CV Gula Manis Sentosa     | Gula          | NET 21        | order@gulamanis.co.id |

**Detail Bank Info:**

-   Semua vendor sudah dilengkapi dengan data NPWP
-   Informasi rekening bank untuk pembayaran
-   Contact person yang jelas

---

### 📦 Products (8 Items)

#### Bahan Baku (5 items):

1. **Tepung Terigu Segitiga Biru** (TP-SEGITIGA-001)

    - Unit: Sak (25 Kg per sak)
    - Purchase Price: Rp 500,000/sak
    - Current Stock: 100 Kg

2. **Telur Ayam Negeri** (TELUR-NEG-001)

    - Unit: Kg
    - Purchase Price: Rp 32,000/kg
    - Current Stock: 50 Kg

3. **Mentega Anchor Unsalted** (MTG-ANCHOR-001)

    - Unit: Kg
    - Purchase Price: Rp 120,000/kg
    - Current Stock: 30 Kg

4. **Gula Pasir Premium** (GULA-PASIR-001)

    - Unit: Kg
    - Purchase Price: Rp 15,000/kg
    - Current Stock: 100 Kg

5. **Ragi Instan** (RAGI-INS-001)
    - Unit: Pack (500g)
    - Purchase Price: Rp 25,000/pack
    - Current Stock: 20 Pack

#### Barang Jadi (3 items):

1. **Roti Tawar Jumbo** (RT-TAWAR-001)

    - Selling Price: Rp 15,000
    - Current Stock: 50 loaf

2. **Croissant Original** (CROIS-ORG-001)

    - Selling Price: Rp 8,000
    - Current Stock: 100 pcs

3. **Kue Nastar Premium** (KUE-NASTAR-001)
    - Selling Price: Rp 45,000 per toples
    - Current Stock: 20 toples

---

### 📋 Purchase Orders (1 PO Ready to Process)

#### PO/2025/12/001 - PT Bogasari Flour Mills

-   **Status:** ✅ Approved (Siap untuk Good Receipt)
-   **Order Date:** 3 hari yang lalu
-   **Expected Delivery:** 2 hari lagi
-   **Items:**
    -   Tepung Terigu Segitiga Biru: 10 Sak @ Rp 500,000
-   **Subtotal:** Rp 5,000,000
-   **Tax (11%):** Rp 550,000
-   **Shipping:** Rp 100,000
-   **Total:** Rp 5,650,000

**Note:** PO ini sudah approved dan siap untuk dibuat Good Receipt!

---

### 🎯 Bill of Materials (2 Recipes)

#### BOM-2025-001: Roti Tawar Jumbo

**Output:** 10 loaf
**Bahan:**

-   Tepung Terigu: 5,000 gram
-   Telur Ayam: 500 gram
-   Mentega: 300 gram
-   Gula Pasir: 400 gram
-   Ragi Instan: 50 gram

#### BOM-2025-002: Croissant Original

**Output:** 20 pcs
**Bahan:**

-   Tepung Terigu: 2,000 gram
-   Mentega: 800 gram
-   Gula Pasir: 200 gram
-   Ragi Instan: 20 gram

---

### 👥 Test Users (5 Users)

| Email                 | Password | Role                 | Permissions          |
| --------------------- | -------- | -------------------- | -------------------- |
| super@admin.com       | password | Super Admin          | All Access ✅        |
| baker@bakery.com      | password | Head Baker           | Production & Recipes |
| inventory@bakery.com  | password | Inventory Controller | Purchasing & Stock   |
| cashier@bakery.com    | password | Cashier              | Sales & POS          |
| production@bakery.com | password | Production Staff     | View Production      |

---

### 💰 Chart of Accounts (32 Accounts)

Struktur akun lengkap untuk bakery:

-   **Aset:** Kas, Bank, Piutang, Persediaan
-   **Liabilitas:** Hutang Usaha, Hutang Lain-lain
-   **Modal:** Modal Pemilik, Laba Ditahan
-   **Pendapatan:** Penjualan Roti, Kue, Pastry
-   **HPP:** Cost per kategori produk
-   **Beban:** Operasional, Gaji, Listrik, Marketing, dll

---

## 🔄 Testing Flow untuk Purchasing

### Step-by-Step Test:

1. **Login** sebagai super_admin atau inventory@bakery.com

2. **Lihat Suppliers:**

    - Menu: Pembelian → Supplier
    - Verify: 5 suppliers tersedia dengan data lengkap

3. **Lihat Purchase Order:**

    - Menu: Pembelian → Order Pembelian
    - Find: PO/2025/12/001 (Status: Approved)
    - Verify: 1 item (Tepung Terigu 10 Sak), Total Rp 5,650,000

4. **Create Good Receipt:**

    - Menu: Pembelian → Terima Barang → Create
    - Select PO: PO/2025/12/001 dari dropdown
    - Auto-populate: Item Tepung Terigu 10 Sak muncul otomatis
    - Input Received Qty: 10 (semua diterima)
    - Input Rejected Qty: 0
    - Click Save

5. **Confirm Receipt:**

    - Di halaman edit Good Receipt yang baru dibuat
    - Click button "Confirm Receipt" (hijau)
    - Confirm: "This will update inventory stock..."
    - Success: "Inventory stock has been updated"

6. **Verify Stock Update:**

    - Menu: Produksi → Produk
    - Find: Tepung Terigu Segitiga Biru
    - Verify: Current Stock bertambah dari 100 Kg menjadi **350 Kg** ✅
        - (100 Kg + 10 Sak × 25 Kg/sak = 350 Kg)

7. **Check PO Status:**
    - Back to: Pembelian → Order Pembelian → PO/2025/12/001
    - Verify: Status berubah menjadi "Received" ✅

---

## 🚀 Cara Refresh Sample Data

Jika ingin reset semua data dan mulai dari awal:

```powershell
php artisan migrate:fresh --seed
```

**Warning:** Perintah ini akan menghapus SEMUA data dan membuat ulang dari sample data!

---

## 📝 Next Steps

Setelah testing purchasing flow berhasil:

1. ✅ Test Good Receipt dengan partial receive (tidak semua diterima)
2. ✅ Test Good Receipt dengan rejection (ada barang ditolak)
3. ⚠️ Implement Payment Module (bayar ke supplier)
4. ⚠️ Create Journal Entry otomatis saat GR confirmed
5. ⚠️ Add Sales/POS Module
6. ⚠️ Add Production Module

---

**Created:** December 8, 2025  
**Version:** BakerySys v2 - Sample Data Documentation
