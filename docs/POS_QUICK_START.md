# 🚀 POS Quick Start Guide

## Cara Menggunakan Point of Sale

### 1. Akses POS

-   Login ke Bakery ERP
-   Klik menu **"Point of Sale"** di sidebar
-   Atau navigate ke `/admin/point-of-sale`

---

### 2. Melakukan Transaksi

#### a. Pilih Produk

1. Gunakan **search bar** atau scroll product grid
2. Klik produk untuk add to cart
3. Produk yang habis stock akan **disabled** (abu-abu)

**Shortcut:** Tekan **F2** untuk focus ke search

#### b. (Optional) Pilih Customer

1. Dropdown "Customer" di sidebar kanan
2. Pilih customer terdaftar ATAU
3. Biarkan "Walk-in Customer" untuk customer umum

**Benefit registered customer:**

-   Dapat loyalty points
-   Auto-discount berdasarkan tier
-   Transaction history

#### c. Atur Keranjang

-   **Tambah qty:** Click tombol **[+]**
-   **Kurang qty:** Click tombol **[-]**
-   **Input manual:** Ketik di box quantity
-   **Hapus item:** Click icon **[X]** merah

**Auto-Calculation:**

-   Subtotal, discount, tax, total akan **update otomatis**

#### d. Proses Pembayaran

1. Click tombol besar **"💳 Bayar Sekarang"**
    - Atau tekan **F4** di keyboard
2. Modal payment akan muncul

**Di Modal Payment:**

1. Pilih **metode pembayaran** (Cash/Card/Transfer/etc)
2. Masukkan **jumlah bayar**
    - Atau click button cepat: **Pas / 50K / 100K**
3. **Kembalian** akan hitung otomatis
4. Click **"✅ Proses Pembayaran"**

**Validasi:**

-   Jumlah bayar HARUS >= Total
-   Tombol akan disabled jika kurang

#### e. Cetak Struk & Selesai

1. Struk otomatis muncul setelah payment
2. Review detail transaksi
3. Click **"🖨️ Print Struk"** (optional)
4. Click **"✨ Transaksi Baru"**
5. Sistem reset, siap transaksi berikutnya!

---

### 3. Keyboard Shortcuts (Kasir Pro!)

| Key     | Action       | Keterangan                           |
| ------- | ------------ | ------------------------------------ |
| **F2**  | Focus Search | Jump ke search box                   |
| **F4**  | Open Payment | Buka modal pembayaran                |
| **ESC** | Clear Cart   | Kosongkan keranjang (ada konfirmasi) |

---

### 4. Tips & Tricks

#### ⚡ Workflow Tercepat:

1. **F2** → ketik nama produk → **Enter**
2. Ulangi untuk produk lain
3. **F4** → pilih payment method → input jumlah → **Enter**
4. **Print** → **Transaksi Baru**

**Tanpa mouse! Pure keyboard!** 🎹

#### 💡 Quick Buttons:

-   **Pas**: Set jumlah bayar = total (tidak ada kembalian)
-   **50K**: Bulatkan ke 50 ribuan terdekat
-   **100K**: Bulatkan ke 100 ribuan terdekat

Contoh: Total Rp 47,500

-   Pas → Rp 47,500
-   50K → Rp 50,000 (kembalian Rp 2,500)
-   100K → Rp 100,000 (kembalian Rp 52,500)

#### 📊 Discount Otomatis:

-   Jika ada discount rule aktif → apply otomatis
-   Jika customer punya tier → tier discount apply
-   Discount name akan muncul di summary
-   **Tidak perlu input manual!**

#### 🎯 Best Practices:

1. **Selalu pilih customer** jika customer terdaftar (untuk points)
2. **Review cart** sebelum payment
3. **Konfirmasi total** dengan customer
4. **Print struk** untuk bukti transaksi
5. **Clear cart** jika customer batal

---

### 5. Troubleshooting

#### "Tombol Bayar disabled (abu-abu)"

→ Keranjang masih kosong, tambah produk dulu

#### "Modal payment tidak muncul"

→ Pastikan ada item di cart, lalu click "Bayar" atau tekan F4

#### "Kembalian tidak muncul"

→ Pastikan jumlah bayar > total

#### "Produk tidak bisa di-click"

→ Stock habis, restock dulu atau pilih produk lain

#### "Discount tidak apply"

→ Check Discount Rules di menu admin, pastikan ada yang aktif

#### "Struk tidak mau print"

→ Pastikan browser allow popup/print dialog

---

### 6. Informasi Penting

**Yang Terjadi Setelah Payment:**

✅ Sales Order dibuat otomatis  
✅ Stock produk berkurang otomatis  
✅ Journal Entry dibuat otomatis (Cash masuk, Revenue, PPN)  
✅ Customer dapat loyalty points (jika registered)  
✅ Customer tier update (jika eligible)  
✅ Stock movement tercatat

**Semua OTOMATIS!** Kasir hanya fokus transaksi.

---

### 7. FAQ

**Q: Bisa batalkan transaksi setelah payment?**  
A: Belum ada fitur void di POS. Hubungi manager/admin untuk refund manual.

**Q: Bisa split payment (2 metode)?**  
A: Belum support. Pilih 1 payment method dominan.

**Q: Bisa kasih discount manual?**  
A: Role kasir tidak bisa. Discount otomatis via rules. Manager bisa edit di Sales Order.

**Q: Struk bisa di-email?**  
A: Belum support. Print lalu foto/scan, atau kirim manual.

**Q: Offline mode?**  
A: Belum support. Harus ada internet/network ke server.

**Q: Barcode scanner support?**  
A: Future feature. Saat ini manual search.

**Q: Bisa lihat history transaksi hari ini?**  
A: Bisa, tapi di menu Sales Orders, filter by today + order_type POS.

**Q: Bisa re-print struk lama?**  
A: Bisa, tapi harus ke Sales Orders → View → (future: print button).

---

## 🎓 Training Checklist untuk Kasir Baru

-   [ ] Login & navigate ke POS
-   [ ] Search & add produk ke cart
-   [ ] Update quantity (tambah, kurang, hapus)
-   [ ] Pilih customer (walk-in vs registered)
-   [ ] Baca summary (subtotal, discount, tax, total)
-   [ ] Buka payment modal (mouse & keyboard F4)
-   [ ] Pilih payment method
-   [ ] Input jumlah bayar & lihat kembalian
-   [ ] Proses payment
-   [ ] Review struk
-   [ ] Print struk
-   [ ] Transaksi baru
-   [ ] Clear cart dengan ESC
-   [ ] Test semua keyboard shortcuts
-   [ ] Handle customer batal (clear cart)
-   [ ] Handle produk stock habis
-   [ ] Simulasi 10 transaksi berturut-turut

**Target:** Kasir bisa 1 transaksi dalam < 1 menit!

---

## ✅ Siap Digunakan!

**Sistem POS sudah production-ready.**  
Kasir tinggal login dan mulai transaksi.

Untuk pertanyaan teknis atau bug report, hubungi IT/Developer.

**Happy Selling! 🎉**
