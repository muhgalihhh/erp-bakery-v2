# 📋 Panduan Stock Adjustment (Penyesuaian Stok)

**Module:** Inventory Management  
**Status:** ✅ Production Ready  
**Version:** 1.0  
**Last Updated:** 10 Desember 2025

---

## 📖 Pengenalan

**Stock Adjustment** adalah modul untuk melakukan penyesuaian stok secara manual ketika terjadi perbedaan antara stok sistem dengan stok fisik. Sangat berguna untuk:

-   🔍 **Stock Opname** (Physical Count) - Pengecekan berkala stok fisik vs sistem
-   💥 **Barang Rusak** - Produk rusak saat produksi/penyimpanan/pengiriman
-   ⏰ **Kadaluarsa** - Produk melewati tanggal expired (critical untuk bakery!)
-   ❓ **Hilang** - Produk hilang karena pencurian/tercecer/dll
-   ✨ **Ketemu** - Produk yang sebelumnya dianggap hilang ternyata ditemukan
-   📝 **Lainnya** - Alasan khusus lainnya

---

## 🎯 Fitur Utama

### ✅ Auto-Numbering

-   Format: **ADJ-YYYYMM-XXXX** (contoh: ADJ-202512-0001)
-   Auto-generated saat create
-   Counter reset setiap bulan

### ✅ Reactive Form

-   Pilih **Produk** → Auto-fill **Stok Sistem** dari `current_stock` produk
-   Input **Stok Aktual** (hasil cek fisik) → Auto-calculate **Selisih**
-   Visual feedback selisih dengan warna:
    -   🟢 **Hijau (+)** jika lebih/ketemu
    -   🔴 **Merah (-)** jika kurang/hilang
    -   ⚫ **Abu-abu (0)** jika sama

### ✅ Approval Workflow

1. **Draft** - Adjustment baru dibuat, belum update stok
2. **Approve** - Update stok sesuai selisih, create Stock Movement record
3. **Cancel** - Batalkan adjustment (hanya jika masih Draft)

### ✅ Audit Trail

-   Track **created_by** (siapa yang buat)
-   Track **approved_by** dan **approved_at** (siapa & kapan approve)
-   Semua perubahan terekam di **Stock Movements** dengan `type=ADJUSTMENT`

---

## 📝 Cara Penggunaan

### 1. Buat Stock Adjustment Baru

**Menu:** `📦 Inventory > Penyesuaian Stok > Create`

**Langkah:**

1. **Tanggal Adjustment** - Pilih tanggal (default: hari ini)
2. **Produk** - Pilih produk yang mau di-adjust
    - Sistem otomatis isi **Stok di Sistem** sesuai data
3. **Stok Aktual/Fisik** - Input hasil cek fisik gudang
    - Sistem otomatis hitung **Selisih** = Aktual - Sistem
    - Contoh: Sistem = 100, Aktual = 95 → Selisih = **-5 (Kurang 5)**
4. **Tipe Adjustment** - Pilih:
    - Stock Opname
    - Barang Rusak
    - Kadaluarsa
    - Lainnya
5. **Alasan** - Pilih alasan detail:
    - Stock Opname
    - Rusak
    - Kadaluarsa
    - Hilang
    - Ketemu
    - Lainnya
6. **Catatan** - Tulis detail tambahan (opsional, tapi recommended!)
    - Contoh: "Stock opname bulanan, ditemukan 5 roti rusak karena packaging bocor"
7. **Save**

**Status setelah Save:** DRAFT (stok belum berubah)

---

### 2. Review & Approve Adjustment

**Menu:** `📦 Inventory > Penyesuaian Stok > [Klik adjustment] > View`

**Langkah:**

1. **Review data** - Pastikan semua data sudah benar
2. **Klik tombol "Setujui Adjustment"** (hijau)
3. **Baca konfirmasi modal** dengan baik:
    - Contoh: "Stok produk 'Roti Coklat' akan mengurangi sebanyak 5.00 Pcs. Pastikan data sudah benar!"
4. **Klik "Ya, Setujui"**

**Yang Terjadi:**

-   ✅ Stok produk di-update sesuai selisih:
    -   Jika selisih **positif (+5)** → `current_stock` naik 5
    -   Jika selisih **negatif (-5)** → `current_stock` turun 5
-   ✅ Status jadi **APPROVED**
-   ✅ Field `approved_by` & `approved_at` terisi
-   ✅ **Stock Movement** baru dibuat dengan:
    -   Type: `ADJUSTMENT`
    -   Reference: `StockAdjustment` model
    -   Reference Number: ADJ-202512-0001
    -   Quantity: sesuai selisih
    -   Balance After: stok setelah update
    -   Notes: alasan adjustment

**Notifikasi:** "Adjustment Berhasil Disetujui - Stok produk 'Roti Coklat' telah diupdate."

---

### 3. Cancel Adjustment (Jika Salah)

**Kondisi:** Hanya bisa cancel adjustment yang masih **DRAFT**

**Langkah:**

1. **View adjustment** yang mau dibatalkan
2. **Klik tombol "Batalkan Adjustment"** (merah)
3. **Konfirmasi:** "Ya, Batalkan"

**Yang Terjadi:**

-   ✅ Status jadi **CANCELLED**
-   ❌ Tidak bisa di-approve lagi
-   ❌ Stok tidak berubah

**Notifikasi:** "Adjustment Dibatalkan - Adjustment ADJ-202512-0001 telah dibatalkan."

---

## 🛡️ Validasi & Aturan

### ✅ Allowed Actions

| Status    | Create | Edit | Approve | Cancel | Delete              |
| --------- | ------ | ---- | ------- | ------ | ------------------- |
| DRAFT     | ✅     | ✅   | ✅      | ✅     | ✅                  |
| APPROVED  | ❌     | ❌   | ❌      | ❌     | ⚠️ Soft delete only |
| CANCELLED | ❌     | ❌   | ❌      | ❌     | ⚠️ Soft delete only |

### 🚫 Restrictions

-   ❌ Tidak bisa approve adjustment yang sudah APPROVED
-   ❌ Tidak bisa cancel adjustment yang sudah APPROVED
-   ❌ Tidak bisa edit adjustment setelah APPROVED/CANCELLED
-   ⚠️ **Hati-hati saat approve** - stok langsung berubah, tidak ada undo!

---

## 📊 Contoh Kasus Nyata

### Kasus 1: Stock Opname Bulanan (Ketemu Kurang)

**Situasi:**

-   Produk: **Tepung Terigu**
-   Stok di Sistem: **100 Kg**
-   Stok di Gudang (hasil cek fisik): **95 Kg**
-   Penyebab: Tercecer saat pindah-pindah karung

**Input:**

-   Tanggal: 1 Desember 2025
-   Produk: Tepung Terigu
-   Stok Sistem: 100 Kg (auto)
-   Stok Aktual: 95 Kg
-   Selisih: **-5 Kg** (Kurang 5 Kg, merah)
-   Tipe: Stock Opname
-   Alasan: Stock Opname
-   Catatan: "Stock opname bulanan, ditemukan kekurangan 5 Kg kemungkinan tercecer"

**Setelah Approve:**

-   `current_stock` Tepung Terigu: 100 → **95 Kg**
-   Stock Movement: TYPE_ADJUSTMENT, qty = -5 Kg, balance_after = 95 Kg

---

### Kasus 2: Barang Rusak (Produksi Gagal)

**Situasi:**

-   Produk: **Roti Coklat**
-   Stok di Sistem: **200 Pcs**
-   Stok Aktual: **185 Pcs** (15 pcs rusak karena gosong)

**Input:**

-   Tanggal: 5 Desember 2025
-   Produk: Roti Coklat
-   Stok Sistem: 200 Pcs (auto)
-   Stok Aktual: 185 Pcs
-   Selisih: **-15 Pcs** (Kurang 15 Pcs, merah)
-   Tipe: Barang Rusak
-   Alasan: Rusak
-   Catatan: "15 roti gosong karena oven terlalu panas, sudah dibuang"

**Setelah Approve:**

-   `current_stock` Roti Coklat: 200 → **185 Pcs**
-   Stock Movement: TYPE_ADJUSTMENT, qty = -15 Pcs, balance_after = 185 Pcs

---

### Kasus 3: Produk Kadaluarsa

**Situasi:**

-   Produk: **Susu Cair**
-   Stok di Sistem: **50 Liter**
-   Stok Aktual: **45 Liter** (5 liter expired & dibuang)

**Input:**

-   Tanggal: 10 Desember 2025
-   Produk: Susu Cair
-   Stok Sistem: 50 Liter (auto)
-   Stok Aktual: 45 Liter
-   Selisih: **-5 Liter** (Kurang 5 Liter, merah)
-   Tipe: Kadaluarsa
-   Alasan: Kadaluarsa
-   Catatan: "5 liter susu expired tanggal 9 Des 2025, sudah dibuang sesuai SOP"

**Setelah Approve:**

-   `current_stock` Susu Cair: 50 → **45 Liter**
-   Stock Movement: TYPE_ADJUSTMENT, qty = -5 Liter, balance_after = 45 Liter

---

### Kasus 4: Produk Ditemukan (Ketemu Lebih)

**Situasi:**

-   Produk: **Gula Pasir**
-   Stok di Sistem: **20 Kg**
-   Stok Aktual: **25 Kg** (ketemu 5 Kg yang kemarin tidak tercatat)

**Input:**

-   Tanggal: 8 Desember 2025
-   Produk: Gula Pasir
-   Stok Sistem: 20 Kg (auto)
-   Stok Aktual: 25 Kg
-   Selisih: **+5 Kg** (Lebih 5 Kg, hijau)
-   Tipe: Lainnya
-   Alasan: Ketemu
-   Catatan: "Ditemukan 1 karung gula (5 Kg) di gudang yang tidak tercatat saat GR kemarin"

**Setelah Approve:**

-   `current_stock` Gula Pasir: 20 → **25 Kg**
-   Stock Movement: TYPE_ADJUSTMENT, qty = +5 Kg, balance_after = 25 Kg

---

## 🎨 UI/UX Features

### Color Coding

-   **Status Badges:**

    -   🟡 **DRAFT** - Warning (kuning)
    -   🟢 **APPROVED** - Success (hijau)
    -   🔴 **CANCELLED** - Danger (merah)

-   **Selisih Display:**
    -   🟢 **+X Kg (Lebih/Ketemu)** - Success (hijau)
    -   🔴 **-X Kg (Kurang/Hilang)** - Danger (merah)
    -   ⚫ **0 Kg (Sama)** - Gray (abu-abu)

### Table Filters

-   **Status:** Draft / Disetujui / Dibatalkan (multi-select)
-   **Tipe:** Stock Opname / Rusak / Kadaluarsa / Lainnya (multi-select)
-   **Produk:** Searchable dropdown
-   **Status Penghapusan:** Dengan/tanpa yang dihapus

### Table Columns

-   **No. Adjustment** - Bold, copyable, primary color
-   **Tanggal** - Format: 10 Des 2025
-   **Produk** - Searchable, description = SKU
-   **Tipe** - Badge dengan warna
-   **Stok Sistem** - Align right, format: 100.00 Kg
-   **Stok Aktual** - Align right, bold, format: 95.00 Kg
-   **Selisih** - Align right, bold, color-coded, format: -5.00 Kg
-   **Alasan** - Hidden by default (toggle)
-   **Status** - Badge dengan warna
-   **Dibuat Oleh** - Hidden by default
-   **Disetujui Oleh** - Hidden by default
-   **Dibuat** - Hidden by default

---

## 🔍 Tracking & Reporting

### Audit Trail di Stock Movements

Setiap approved adjustment otomatis create record di `stock_movements`:

```
Type: ADJUSTMENT
Reference Type: App\Models\StockAdjustment
Reference ID: <UUID adjustment>
Reference Number: ADJ-202512-0001
Product: Roti Coklat
Quantity: -5.00
UOM: Pcs
Balance After: 195.00
Notes: "Stock Opname - Kurang 5 Pcs (Rusak)"
Created By: <User yang approve>
Created At: 2025-12-10 22:15:00
```

### Cara Lihat History

1. **Menu:** `📦 Inventory > Stock Movements` (coming soon!)
2. **Filter:** Type = ADJUSTMENT
3. **View:** Semua adjustment yang sudah di-approve

### Reporting (Future)

-   Stock Adjustment Summary by Period
-   Most Damaged Products Report
-   Expired Products Trend
-   Adjustment Reason Analysis

---

## 🐛 Troubleshooting

### Problem: "Gagal Menyetujui Adjustment"

**Penyebab:**

-   Adjustment sudah pernah di-approve sebelumnya
-   Database error (connection timeout)

**Solusi:**

-   Cek status adjustment, jika sudah APPROVED tidak bisa approve lagi
-   Refresh page dan coba lagi
-   Jika masih error, hubungi admin sistem

---

### Problem: Tombol "Setujui" tidak muncul

**Penyebab:**

-   Adjustment sudah APPROVED atau CANCELLED
-   User tidak punya permission untuk approve

**Solusi:**

-   Cek status adjustment di detail page
-   Jika masih DRAFT, cek role & permission user
-   Hubungi admin untuk grant permission jika diperlukan

---

### Problem: Selisih tidak ter-calculate

**Penyebab:**

-   Browser cache issue
-   JavaScript error di form

**Solusi:**

-   Clear browser cache
-   Hard refresh: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
-   Coba browser lain (Chrome/Firefox/Edge)

---

## 📚 Best Practices

### ✅ DO's

-   ✅ **Selalu isi Catatan** dengan detail yang jelas
-   ✅ **Double-check** data sebelum approve (tidak ada undo!)
-   ✅ **Lakukan stock opname berkala** (minimal 1x per bulan)
-   ✅ **Segera input adjustment** setelah menemukan perbedaan stok
-   ✅ **Dokumentasikan** alasan adjustment dengan baik
-   ✅ **Review approved adjustments** secara berkala untuk mencari pola masalah

### ❌ DON'Ts

-   ❌ **Jangan approve tanpa pengecekan ulang**
-   ❌ **Jangan gunakan** adjustment untuk mengganti transaksi normal (GR/MO/Sales)
-   ❌ **Jangan approve** adjustment orang lain tanpa verifikasi fisik
-   ❌ **Jangan skip catatan** - catatan penting untuk audit!
-   ❌ **Jangan gunakan** tipe "Lainnya" kalau ada tipe yang lebih spesifik

---

## 🔐 Security & Permissions

**Recommended Permissions Setup:**

| Role                  | Create | View | Edit | Approve | Cancel | Delete |
| --------------------- | ------ | ---- | ---- | ------- | ------ | ------ |
| **Admin**             | ✅     | ✅   | ✅   | ✅      | ✅     | ✅     |
| **Warehouse Manager** | ✅     | ✅   | ✅   | ✅      | ✅     | ❌     |
| **Warehouse Staff**   | ✅     | ✅   | ✅   | ❌      | ❌     | ❌     |
| **Accountant**        | ❌     | ✅   | ❌   | ❌      | ❌     | ❌     |
| **Production**        | ❌     | ✅   | ❌   | ❌      | ❌     | ❌     |

**Rationale:**

-   **Approve** hanya untuk Manager+ karena langsung update stok
-   **Accountant** view-only untuk audit
-   **Production** view-only untuk info ketersediaan bahan

---

## 📞 Support

**Developer:** GitHub Copilot + Ifan  
**Documentation:** STOCK_ADJUSTMENT_GUIDE.md  
**Last Updated:** 10 Desember 2025  
**Version:** 1.0 (Production Ready)

**For Issues/Questions:**

-   Check INVENTORY_PROGRESS.md untuk status development
-   Check NAVIGATION_STRUCTURE.md untuk struktur menu
-   Review code di `app/Models/StockAdjustment.php` untuk business logic

---

**Happy Adjusting! 📦✨**
