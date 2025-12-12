# POS Modern Interface - Panduan Tampilan Kasir

## 🎯 Perubahan yang Telah Dilakukan

Tampilan Point of Sale (POS) telah diubah dari layout standard Filament menjadi tampilan **kasir modern full-screen** yang lebih user-friendly dan profesional.

## 📋 Fitur Tampilan Baru

### 1. **Header Kasir Custom**

-   **Logo dan Branding** - Menampilkan nama aplikasi dengan icon yang menarik
-   **Tanggal & Waktu Real-time** - Update otomatis setiap detik
-   **Info Kasir** - Menampilkan nama user yang sedang login
-   **Tombol Kembali** - Navigasi mudah ke daftar transaksi
-   **Gradient Background** - Tampilan profesional dengan gradien biru-ungu

### 2. **Layout Full-Screen**

-   **Tanpa Sidebar** - Lebih banyak ruang untuk produk dan keranjang
-   **Tanpa Header Filament** - Layout bersih khusus untuk kasir
-   **Responsive Design** - Berfungsi di berbagai ukuran layar
-   **Custom Scrollbar** - Scrollbar cantik dengan gradien

### 3. **Section Produk (Kiri)**

#### Search Bar

-   Input pencarian dengan icon kaca pembesar
-   Live search dengan debounce 300ms
-   Placeholder yang jelas

#### Filter Kategori

-   Tombol kategori horizontal dengan scroll
-   Active state dengan gradien warna
-   Tombol "Semua Produk" untuk reset filter

#### Grid Produk

-   Layout grid responsive (2-5 kolom tergantung layar)
-   Card produk dengan:
    -   **Placeholder gambar** dengan gradien
    -   **Badge stok** (merah untuk habis, orange untuk <10)
    -   **Hover effect** dengan scale transform
    -   **Nama produk** dengan line-clamp
    -   **SKU** dengan warna abu-abu
    -   **Harga** dengan format rupiah
    -   **Background gradient** saat hover

### 4. **Section Keranjang & Checkout (Kanan)**

#### Customer Selection

-   Dropdown customer dengan pencarian
-   Default "Walk-in Customer"
-   Background gradien biru-ungu muda

#### Cart Items

-   Animasi slide-in saat item ditambahkan
-   Setiap item menampilkan:
    -   **Nama & SKU produk**
    -   **Harga satuan**
    -   **Quantity controls** dengan tombol +/-
    -   **Input manual quantity**
    -   **Subtotal per item**
    -   **Tombol hapus** dengan icon tempat sampah
-   Border hover yang berubah warna

#### Summary & Total

-   **Subtotal** - Total sebelum diskon dan pajak
-   **Diskon** - Tampil jika ada diskon otomatis (warna hijau)
-   **Pajak** - Dengan persentase yang ditampilkan
-   **TOTAL** - Besar dan bold dengan warna biru
-   Semua angka dalam format Rupiah Indonesia

#### Action Buttons

-   **Clear Button** - Hapus semua item di keranjang
-   **Bayar Button** - Proses pembayaran dengan gradien menarik
-   Grid 2 kolom dengan icon yang jelas

### 5. **Empty States**

-   Keranjang kosong: Icon shopping cart dengan pesan yang jelas
-   Produk tidak ditemukan: Icon box dengan saran aksi

## 🎨 Design Elements

### Color Scheme

-   **Primary Gradient**: Blue (#667eea) to Purple (#764ba2)
-   **Success**: Green untuk diskon dan notifikasi sukses
-   **Warning**: Orange untuk stok rendah
-   **Danger**: Red untuk stok habis dan tombol delete
-   **Background**: Gray-50 untuk area utama

### Typography

-   **Title**: Text-xl dan bold untuk header
-   **Product Name**: Text-sm dan bold
-   **Prices**: Text-lg dan bold dengan warna biru
-   **Labels**: Text-sm dengan gray-700

### Animations

-   **Slide-in** untuk cart items
-   **Scale transform** untuk product cards dan buttons
-   **Smooth transitions** di semua interaksi (200-300ms)

## 💻 Technical Implementation

### File Structure

```
resources/views/filament/
├── layouts/
│   └── pos-layout.blade.php          # Custom layout tanpa sidebar/header
└── resources/
    └── point-of-sale-resource/
        └── pages/
            └── point-of-sale-page.blade.php  # Tampilan kasir modern
```

### Key Features di PointOfSalePage.php

```php
protected static string $layout = 'filament.layouts.pos-layout';
```

-   Menggunakan custom layout, bukan layout Filament default

### Livewire Properties

-   `$searchProduct` - Untuk live search produk
-   `$selectedCategory` - Untuk filter kategori
-   `$cart` - Array items di keranjang
-   `$selectedCustomerId` - Customer yang dipilih
-   `$filteredProducts` - Produk yang ditampilkan
-   `$categories` - Daftar kategori produk

### Methods Penting

-   `loadProducts()` - Load semua produk yang bisa dijual
-   `loadCategories()` - Load kategori yang tersedia
-   `filterProducts()` - Filter berdasarkan search & kategori
-   `addToCart()` - Tambah produk ke keranjang
-   `incrementQuantity()` / `decrementQuantity()` - Ubah quantity
-   `calculateTotals()` - Hitung subtotal, diskon, pajak, total
-   `proceedToPayment()` - Lanjut ke pembayaran
-   `processPayment()` - Proses transaksi final

## 🚀 Cara Menggunakan

### Untuk Kasir:

1. **Pilih Customer (Opsional)**

    - Klik dropdown customer
    - Pilih customer atau biarkan "Walk-in Customer"

2. **Tambah Produk**

    - Gunakan search bar untuk cari produk
    - Atau klik kategori untuk filter
    - Klik card produk untuk tambah ke keranjang

3. **Atur Quantity**

    - Klik tombol + untuk tambah
    - Klik tombol - untuk kurang
    - Atau ketik langsung di input

4. **Review Total**

    - Lihat subtotal, diskon otomatis, dan pajak
    - Total akan update otomatis

5. **Proses Pembayaran**

    - Klik tombol "Bayar"
    - Sistem otomatis proses untuk cash payment
    - Notifikasi sukses akan muncul
    - Struk dapat dicetak

6. **Transaksi Berikutnya**
    - Keranjang otomatis reset setelah pembayaran
    - Siap untuk transaksi baru

### Keyboard Shortcuts (Future Enhancement)

-   `Ctrl + F` - Focus ke search bar
-   `Ctrl + K` - Clear keranjang
-   `Enter` - Proses pembayaran (jika keranjang tidak kosong)

## 🔧 Customization

### Mengubah Warna Gradient

Edit di `pos-layout.blade.php`:

```blade
<div class="bg-gradient-to-r from-blue-600 to-purple-600">
```

### Mengubah Grid Produk

Edit di `point-of-sale-page.blade.php`:

```blade
grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5
```

### Menambah Payment Method Options

Tambahkan modal pembayaran di method `proceedToPayment()` untuk pilih metode bayar selain cash.

## 📱 Responsive Breakpoints

-   **Mobile** (< 768px): 2 kolom produk
-   **Tablet** (768px - 1024px): 3 kolom produk
-   **Desktop** (1024px - 1280px): 4 kolom produk
-   **Large Desktop** (> 1280px): 5 kolom produk

## ⚡ Performance Tips

1. **Product Loading**: Hanya load produk yang `is_sellable = true` dan `stock > 0`
2. **Live Search**: Menggunakan debounce 300ms untuk hindari request berlebihan
3. **Lazy Loading**: Kategori dan customer di-load saat mount
4. **Optimized Calculations**: Total di-calculate otomatis saat cart berubah

## 🎯 Next Improvements

1. **Payment Modal** - Modal untuk pilih metode bayar dan input jumlah bayar
2. **Barcode Scanner** - Support scan barcode produk
3. **Receipt Printing** - Cetak struk otomatis
4. **Hot Keys** - Keyboard shortcuts
5. **Product Images** - Upload dan tampilkan gambar produk
6. **Sound Effects** - Suara saat scan atau checkout
7. **Customer Display** - Second screen untuk customer
8. **Cash Drawer Integration** - Integrasi dengan laci kas

## 📚 References

-   Layout custom: `resources/views/filament/layouts/pos-layout.blade.php`
-   View utama: `resources/views/filament/resources/point-of-sale-resource/pages/point-of-sale-page.blade.php`
-   Controller: `app/Filament/Resources/PointOfSaleResource/Pages/PointOfSalePage.php`

---

**Catatan**: Tampilan ini dirancang untuk memberikan pengalaman kasir yang cepat, efisien, dan modern. Semua elemen dioptimalkan untuk kecepatan transaksi maksimal.
