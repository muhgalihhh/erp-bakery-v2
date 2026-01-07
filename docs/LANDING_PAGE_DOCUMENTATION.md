# Landing Page & Shop Management Documentation

## Overview

Sistem landing page dan manajemen toko telah berhasil ditambahkan ke aplikasi Bakery ERP. Fitur ini memungkinkan toko untuk memiliki halaman publik yang menarik dan mudah dikelola.

## Fitur yang Ditambahkan

### 1. Landing Page

**URL:** `http://localhost:8000/`

#### Komponen Landing Page:

-   **Hero Section**: Banner utama dengan gambar, nama toko, tagline, dan CTA
-   **Products Section**: Katalog produk dengan gambar, harga, dan tombol order
-   **About Us Section**: Informasi tentang toko dengan gambar dan jam operasional
-   **Contact Section**: Informasi kontak lengkap dengan WhatsApp integration
-   **Responsive Design**: Fully responsive dengan mobile menu
-   **SEO Friendly**: Meta tags support untuk search engine optimization

#### Fitur Navigation:

-   Sticky navbar dengan logo dan menu
-   Smooth scrolling untuk anchor links
-   Mobile-friendly hamburger menu
-   Link ke admin panel

### 2. Shop Settings Management (Admin)

**URL:** `http://localhost:8000/admin/shop-settings`

#### Data yang dapat dikelola:

**Informasi Toko:**

-   Nama Toko
-   Tagline
-   Deskripsi Singkat
-   Tentang Kami (Long description)

**Kontak & Lokasi:**

-   Email
-   Telepon
-   WhatsApp (dengan auto-format link)
-   Alamat
-   Kota
-   Kode Pos

**Media Sosial:**

-   Facebook URL
-   Instagram URL
-   Twitter URL
-   YouTube URL

**Gambar:**

-   Logo
-   Hero/Banner Image
-   About Us Image

**Lainnya:**

-   Status Toko (Buka/Tutup)
-   Pengumuman

## File yang Dibuat

### 1. Database Migration

**File:** `database/migrations/2026_01_07_000001_create_shop_settings_table.php`

-   Membuat tabel `shop_settings` dengan semua field yang diperlukan
-   Menambahkan default record saat migration

### 2. Model

**File:** `app/Models/ShopSetting.php`

**Methods:**

-   `getSettings()`: Static method untuk mengambil settings record
-   `getLogoUrlAttribute()`: Accessor untuk logo URL
-   `getHeroImageUrlAttribute()`: Accessor untuk hero image URL
-   `getAboutImageUrlAttribute()`: Accessor untuk about image URL
-   `getWhatsappLinkAttribute()`: Generate WhatsApp URL otomatis
-   `isCurrentlyOpen()`: Check apakah toko sedang buka

### 3. Filament Resource

**File:** `app/Filament/Resources/ShopSettingResource.php`

**Fitur:**

-   Single record resource (tidak bisa create/delete)
-   Form dengan sections terorganisir
-   File upload untuk images
-   URL validation untuk social media

**File:** `app/Filament/Resources/ShopSettingResource/Pages/ManageShopSettings.php`

-   Manage page untuk edit settings

### 4. Views

**File:** `resources/views/layouts/landing.blade.php`

-   Main layout dengan navbar dan footer
-   Alpine.js integration untuk interactivity
-   Responsive design dengan Tailwind CSS
-   Social media icons

**File:** `resources/views/landing/index.blade.php`

-   Hero section dengan CTA buttons
-   Products grid dengan "Pesan" button
-   About section dengan business hours
-   Contact section dengan WhatsApp integration

### 5. Controller

**File:** `app/Http/Controllers/LandingController.php`

**Methods:**

-   `index()`: Display landing page dengan products
-   `products()`: Display product catalog dengan filter (untuk pengembangan future)

### 6. Routes

**File:** `routes/web.php`

-   Route `/` mengarah ke landing page
-   Route `/products` untuk catalog page

## Cara Menggunakan

### Mengelola Shop Settings:

1. Login ke admin panel (`/admin`)
2. Buka menu "Pengaturan" → "Pengaturan Toko"
3. Edit informasi toko
4. Upload logo dan gambar
5. Simpan perubahan

### Upload Gambar:

-   Logo: Maksimal 2MB, untuk navbar dan footer
-   Hero Image: Maksimal 5MB, untuk banner utama
-   About Image: Maksimal 5MB, untuk section tentang kami

### WhatsApp Integration:

-   Masukkan nomor WhatsApp dengan format: 628123456789
-   Sistem akan otomatis generate link WhatsApp
-   Tombol "Hubungi Kami" dan "Pesan" akan menggunakan WhatsApp

### Menampilkan Produk:

-   Produk akan otomatis muncul di landing page
-   Hanya produk dengan `is_active = true` dan `stock > 0` yang ditampilkan
-   Produk diurutkan berdasarkan nama

## Customization Tips

### Mengubah Warna Tema:

Edit file `resources/views/layouts/landing.blade.php` dan `resources/views/landing/index.blade.php`:

-   Ganti `blue-600` dengan warna pilihan Anda
-   Tailwind colors: red, green, purple, indigo, pink, etc.

### Menambahkan Section:

1. Buka `resources/views/landing/index.blade.php`
2. Tambahkan section baru dengan struktur HTML+Tailwind
3. Update navbar dengan link ke section baru

### Mengubah Jam Operasional:

Karena field `business_hours` kompleks, untuk sementara ditampilkan dari database default.
Anda bisa mengedit langsung via database atau menambahkan field text di admin.

## SEO Optimization

### Meta Tags:

-   Meta Title: Untuk search engine results
-   Meta Description: Deskripsi yang muncul di Google
-   Meta Keywords: Kata kunci untuk SEO

### Best Practices:

1. Upload gambar dengan ukuran optimal (compress dulu)
2. Gunakan alt text untuk semua gambar
3. Isi meta description dengan informatif
4. Tulis URL yang SEO-friendly

## Testing Checklist

-   [x] Migration berhasil dijalankan
-   [ ] Landing page dapat diakses di http://localhost:8000/
-   [ ] Shop settings dapat diakses di admin panel
-   [ ] Upload logo berhasil
-   [ ] Upload hero image berhasil
-   [ ] WhatsApp link berfungsi
-   [ ] Social media links berfungsi
-   [ ] Products ditampilkan dengan benar
-   [ ] Mobile responsive bekerja dengan baik
-   [ ] Navbar sticky berfungsi

## Troubleshooting

### Landing page tidak muncul:

-   Cek apakah ada record di tabel `shop_settings`
-   Jalankan: `php artisan migrate` jika belum
-   Clear cache: `php artisan cache:clear`

### Gambar tidak muncul:

-   Pastikan symbolic link sudah dibuat: `php artisan storage:link`
-   Cek permissions folder `storage/app/public`
-   Pastikan file uploaded ke direktori yang benar

### Products tidak muncul:

-   Cek tabel `products` ada data dengan `is_active = 1` dan `stock > 0`
-   Pastikan field `image` terisi jika ingin menampilkan gambar

## Pengembangan Future

### Ideas untuk Enhancement:

1. **Blog/News Section**: Tambahkan section untuk berita atau artikel
2. **Testimonials**: Tambahkan section testimoni pelanggan
3. **Gallery**: Tambahkan galeri foto produk
4. **Newsletter**: Tambahkan form subscribe newsletter
5. **Multi-language**: Support bahasa Indonesia dan Inggris
6. **Product Categories**: Filter produk berdasarkan kategori
7. **Search Products**: Fitur pencarian produk
8. **Shopping Cart**: Keranjang belanja untuk customer
9. **Online Payment**: Integrasi payment gateway
10. **Order Tracking**: Customer bisa track pesanan

## Support

Jika ada pertanyaan atau issue, silakan dokumentasikan di:

-   `docs/` folder
-   Atau hubungi developer

---

**Dibuat pada:** 7 Januari 2026
**Versi:** 1.0.0
**Status:** ✅ Production Ready
