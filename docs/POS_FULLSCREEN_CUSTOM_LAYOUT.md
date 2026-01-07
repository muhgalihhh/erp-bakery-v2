# POS Fullscreen Mode - Custom Layout

## Overview

Halaman POS Fullscreen telah diupdate untuk menggunakan layout custom yang tidak terikat dengan Filament Admin Panel. Ini memberikan pengalaman POS yang lebih fleksibel dan customizable.

## Files Created/Modified

### ✅ Files Created

1. **`resources/views/layouts/pos.blade.php`**
    - Layout khusus untuk POS tanpa Filament
    - Include Tailwind CSS custom styles
    - Notification system built-in
    - Keyboard shortcuts support
    - Real-time clock
2. **`resources/views/pos/fullscreen.blade.php`**
    - View fullscreen untuk POS
    - Header dengan branding dan user info
    - Quick shortcuts info di bottom bar
    - Optimized untuk penggunaan fullscreen

### ✅ Files Modified

1. **`routes/web.php`**
    - Route `/pos/fullscreen` sekarang menggunakan view `pos.fullscreen`
2. **`app/Livewire/Pos/PointOfSale.php`**
    - Tambah property `isFullscreen` untuk detect fullscreen mode
    - Update method `mount()` untuk terima parameter fullscreen
3. **`resources/views/livewire/pos/point-of-sale.blade.php`**
    - Conditional styling untuk fullscreen mode

## Features

### 🎨 Custom Styling

-   **Tailwind CSS** - Full customization dengan utility-first CSS
-   **No Filament Dependencies** - Tidak ada styling Filament yang membatasi
-   **Gradient Backgrounds** - Custom gradient untuk header dan cards
-   **Responsive Design** - Optimized untuk berbagai ukuran layar

### ⌨️ Keyboard Shortcuts

-   **F9** - Open payment modal (Quick checkout)
-   **F8** - Clear cart
-   **ESC** - Close all modals

### 🔔 Notification System

-   Success notifications (hijau)
-   Error notifications (merah)
-   Warning notifications (kuning)
-   Info notifications (biru)
-   Auto-dismiss setelah 3 detik

### 🕐 Real-time Clock

-   Menampilkan waktu real-time di header
-   Update setiap detik
-   Format Indonesia (HH:mm:ss)

## Usage

### Akses Halaman

```
http://localhost:8000/pos/fullscreen
```

### Authentication & Permission

-   User harus login terlebih dahulu
-   Membutuhkan permission: `page_Pos` atau `view_pos`
-   Auto redirect ke login jika belum authenticated

## Customization Guide

### Mengubah Warna Theme

Edit file `resources/views/layouts/pos.blade.php`:

```css
.pos-gradient {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    /* Ubah warna sesuai keinginan */
}
```

### Mengubah Header

Edit file `resources/views/pos/fullscreen.blade.php` pada section `Top Navigation Bar`.

### Menambah Shortcuts

Edit file `resources/views/layouts/pos.blade.php` pada section JavaScript keyboard shortcuts:

```javascript
document.addEventListener("keydown", function (e) {
    // Tambahkan shortcut baru disini
    if (e.key === "F10") {
        e.preventDefault();
        // Your custom action
    }
});
```

### Custom Notification

Trigger dari Livewire component:

```php
$this->dispatch('notify', [
    'type' => 'success', // success, error, warning, info
    'message' => 'Your message here'
]);
```

## Layout Structure

```
┌─────────────────────────────────────────────┐
│  Header (Gradient Blue)                     │
│  - Back button | Logo | Clock | User Info   │
├─────────────────────────────────────────────┤
│                                             │
│  Main Content (Livewire Component)          │
│  - Product Grid (Left)                      │
│  - Cart & Checkout (Right)                  │
│                                             │
├─────────────────────────────────────────────┤
│  Bottom Bar (Shortcuts Info)                │
│  F9: Bayar | F8: Kosongkan | ESC: Close     │
└─────────────────────────────────────────────┘
```

## Differences: Filament vs Custom Layout

| Feature            | Filament Layout | Custom Layout      |
| ------------------ | --------------- | ------------------ |
| Sidebar            | ✅ Ada          | ❌ Tidak ada       |
| Filament Widgets   | ✅ Ya           | ❌ Tidak           |
| Custom Styling     | ⚠️ Terbatas     | ✅ Full control    |
| Keyboard Shortcuts | ❌ Tidak        | ✅ Ada             |
| Fullscreen         | ⚠️ Partial      | ✅ True fullscreen |
| Performance        | ⚠️ Heavy        | ✅ Light           |

## Performance Benefits

1. **Faster Load Time** - Tidak load Filament assets yang tidak diperlukan
2. **Lighter Bundle** - Hanya load CSS/JS yang benar-benar dipakai
3. **Better UX** - Optimized untuk workflow POS
4. **Customizable** - Mudah disesuaikan dengan kebutuhan

## Browser Compatibility

-   ✅ Chrome/Edge (Recommended)
-   ✅ Firefox
-   ✅ Safari
-   ⚠️ IE11 (Not tested)

## Next Steps / Future Enhancements

-   [ ] Add print receipt functionality
-   [ ] Add barcode scanner support (hardware)
-   [ ] Add offline mode (PWA)
-   [ ] Add customer display screen
-   [ ] Add sound notifications
-   [ ] Add dark mode toggle

## Support

Jika ada kendala atau pertanyaan, silakan hubungi tim development.

---

**Last Updated:** January 7, 2026
**Version:** 2.0.0
