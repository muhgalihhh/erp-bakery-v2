# Quick Start Guide - POS Fullscreen

## 🚀 Cara Mengakses POS Fullscreen

### Dari Dashboard Admin

1. Login ke dashboard admin: `http://localhost:8000/admin`
2. Klik menu **"Point of Sale"** di sidebar (Sales group)
3. Klik tombol **"Buka Kasir / POS"**
4. Halaman POS fullscreen akan terbuka di tab baru

### Akses Langsung

Buka URL: `http://localhost:8000/pos/fullscreen`

## 📋 Fitur Utama

### 1. Produk Grid

-   Pencarian produk by nama, SKU, atau barcode
-   Grid produk dengan gambar dan harga
-   Indikator stok (rendah/habis)
-   Badge discount untuk produk promo

### 2. Cart Management

-   Add/remove produk
-   Update quantity dengan +/- button atau input manual
-   Auto-calculate subtotal
-   Pilih customer (optional)

### 3. Customer Features

-   Pilih customer untuk apply member discount
-   Birthday detection & special discount
-   Points tracking
-   Tier-based discounts

### 4. Checkout

-   Multiple payment methods (Cash, QRIS, Transfer, etc)
-   Auto-calculate tax & discount
-   Points redemption
-   Print receipt

## ⌨️ Keyboard Shortcuts

| Shortcut | Function                      |
| -------- | ----------------------------- |
| `F9`     | Open payment modal (Checkout) |
| `F8`     | Clear cart                    |
| `ESC`    | Close all modals              |

## 🎨 Customization

### Mengubah Warna Header

File: `resources/views/layouts/pos.blade.php`

```css
.pos-gradient {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
}
```

Ganti dengan warna pilihan Anda:

-   Hijau: `linear-gradient(135deg, #065f46 0%, #10b981 100%)`
-   Merah: `linear-gradient(135deg, #991b1b 0%, #dc2626 100%)`
-   Purple: `linear-gradient(135deg, #581c87 0%, #a855f7 100%)`

### Mengubah Logo/Title

File: `resources/views/pos/fullscreen.blade.php`

Cari section:

```blade
<h1 class="text-2xl font-bold leading-tight">Point of Sale</h1>
<p class="text-white/70 text-sm">{{ config('app.name', 'Bakery ERP') }}</p>
```

## 🔧 Troubleshooting

### Error 403 - Access Denied

**Penyebab:** User tidak punya permission untuk akses POS
**Solusi:**

1. Login ke admin dashboard
2. Buka Settings → Users & Permissions
3. Assign role yang punya permission `page_Pos` atau `view_pos`

### Halaman Blank/Error

**Penyebab:** Cache issue atau file tidak ter-compile
**Solusi:**

```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

### Livewire Not Working

**Penyebab:** Assets belum di-build
**Solusi:**

```bash
npm run build
# atau untuk development:
npm run dev
```

### Keyboard Shortcuts Tidak Berfungsi

**Penyebab:** Focus tidak di halaman
**Solusi:** Click di area halaman terlebih dahulu

## 💡 Tips & Tricks

### 1. Barcode Scanner

Jika menggunakan barcode scanner hardware, pastikan:

-   Scanner dalam mode "keyboard emulation"
-   Scanner mengirim ENTER setelah scan
-   Focus ada di halaman POS

### 2. Multiple Tabs

Anda bisa buka multiple POS fullscreen di tab berbeda untuk multiple kasir.

### 3. Fullscreen Browser

Tekan `F11` di browser untuk true fullscreen mode (hide browser toolbar).

### 4. Print Receipt

Setelah checkout, akan muncul modal receipt. Tekan CTRL+P atau gunakan tombol Print.

## 📱 Mobile/Tablet

Layout responsive dan bisa digunakan di:

-   ✅ Desktop (Recommended)
-   ✅ Tablet (iPad, Android tablet)
-   ⚠️ Mobile (Terbatas, tidak direkomendasikan)

## 🆘 Support

Jika ada masalah, cek:

1. Browser console untuk error (F12)
2. Laravel log: `storage/logs/laravel.log`
3. Livewire network request di DevTools

---

**Happy Selling! 🛒**
