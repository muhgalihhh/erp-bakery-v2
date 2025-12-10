# 🎯 Panduan Lengkap Discount Rules System

## 📚 Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Cara Membuat Discount Rule](#cara-membuat-discount-rule)
3. [Format JSON Kondisi](#format-json-kondisi)
4. [Format JSON Aksi](#format-json-aksi)
5. [Contoh Kasus Nyata](#contoh-kasus-nyata)
6. [Tips & Best Practices](#tips--best-practices)

---

## 📖 Pengenalan

Discount Rules adalah sistem promosi yang sangat fleksibel yang memungkinkan Anda membuat berbagai jenis promosi dengan kondisi dan aksi yang dapat dikustomisasi.

### Komponen Utama:

-   **Kondisi (Conditions)**: Syarat-syarat yang harus dipenuhi
-   **Aksi (Actions)**: Diskon/benefit yang diberikan jika kondisi terpenuhi
-   **Prioritas**: Urutan penerapan jika ada multiple discount
-   **Usage Limit**: Batasan berapa kali bisa dipakai

---

## 🆕 Cara Membuat Discount Rule

### Langkah 1: Buka Menu Discount Rules

1. Login ke admin panel
2. Klik menu **"Discount Rules"**
3. Klik tombol **"Create"**

### Langkah 2: Isi Informasi Dasar

-   **Nama Promosi**: Nama yang mudah diingat (contoh: "Grand Opening 20%")
-   **Kode Unik**: Kosongkan untuk auto-generate
-   **Deskripsi**: Jelaskan detail promosi

### Langkah 3: Atur Kode Kupon (Opsional)

-   **Kode Kupon**: Isi jika customer harus input kode (contoh: "GRAND20")
-   **Promosi Publik**:
    -   ON = Semua customer bisa pakai tanpa kode
    -   OFF = Harus input kode kupon

### Langkah 4: Tentukan Jadwal

-   **Mulai Berlaku**: Tanggal & waktu mulai
-   **Berakhir**: Tanggal & waktu selesai

### Langkah 5: Atur Prioritas

-   **Prioritas**: 0-100 (semakin tinggi semakin prioritas)
-   **Bisa Dikombinasi**: ON jika bisa pakai bareng discount lain

### Langkah 6: Tentukan Kondisi (JSON)

Lihat [Format JSON Kondisi](#format-json-kondisi)

### Langkah 7: Tentukan Aksi Diskon (JSON)

Lihat [Format JSON Aksi](#format-json-aksi)

### Langkah 8: Atur Batasan Penggunaan

-   **Total Limit**: Berapa kali total bisa dipakai (semua customer)
-   **Limit Per Customer**: Berapa kali 1 customer bisa pakai

### Langkah 9: Aktifkan Promosi

-   Toggle **"Aktifkan Promosi"** menjadi ON

### Langkah 10: Save

Klik tombol **"Create"**

---

## ✅ Format JSON Kondisi

### 1️⃣ Kondisi Berdasarkan Total Belanja

```json
{
    "min_subtotal": 200000,
    "max_subtotal": 1000000
}
```

**Arti**: Berlaku untuk pembelian Rp 200.000 - Rp 1.000.000

---

### 2️⃣ Kondisi Berdasarkan Produk Tertentu

```json
{
    "required_product_ids": [1, 5, 10],
    "min_quantity": 2
}
```

**Arti**: Customer harus beli minimal 2 dari produk ID 1, 5, atau 10

---

### 3️⃣ Kondisi Berdasarkan Kategori

```json
{
    "required_category_ids": [2, 3],
    "min_subtotal": 100000
}
```

**Arti**: Berlaku jika beli dari kategori ID 2 atau 3, minimal Rp 100.000

---

### 4️⃣ Kondisi Berdasarkan Customer Tier

```json
{
    "customer_tier_codes": ["GOLD", "PLATINUM", "VIP"],
    "min_subtotal": 150000
}
```

**Arti**: Hanya untuk member Gold/Platinum/VIP dengan belanja min Rp 150.000

---

### 5️⃣ Kondisi Berdasarkan Hari (Weekend Special)

```json
{
    "day_of_week": [6, 0],
    "min_subtotal": 100000
}
```

**Arti**: Berlaku Sabtu (6) & Minggu (0), min pembelian Rp 100.000

**Catatan**:

-   0 = Minggu
-   1 = Senin
-   2 = Selasa
-   3 = Rabu
-   4 = Kamis
-   5 = Jumat
-   6 = Sabtu

---

### 6️⃣ Kondisi Berdasarkan Jam (Happy Hour)

```json
{
    "time_range": ["14:00", "17:00"],
    "min_subtotal": 80000
}
```

**Arti**: Berlaku jam 14:00-17:00 (sore hari)

---

### 7️⃣ Kondisi Ulang Tahun

```json
{
    "is_birthday": true,
    "min_subtotal": 50000
}
```

**Arti**: Hanya untuk customer yang sedang ulang tahun hari ini

---

### 8️⃣ Kondisi First Purchase

```json
{
    "is_first_purchase": true,
    "min_subtotal": 300000
}
```

**Arti**: Hanya untuk customer yang belanja pertama kali

---

### 9️⃣ Kombinasi Kondisi Kompleks

```json
{
    "min_subtotal": 500000,
    "customer_tier_codes": ["PLATINUM", "VIP"],
    "day_of_week": [6, 0],
    "required_category_ids": [2]
}
```

**Arti**: Member Platinum/VIP yang beli kategori ID 2 minimal Rp 500.000 di weekend

---

## 🎯 Format JSON Aksi

### 1️⃣ Diskon Persentase

```json
{
    "discount_type": "percentage",
    "discount_value": 20,
    "max_discount_amount": 100000,
    "apply_to": "order"
}
```

**Arti**: Diskon 20% untuk total order, maksimal potongan Rp 100.000

---

### 2️⃣ Diskon Nominal Tetap

```json
{
    "discount_type": "fixed",
    "discount_value": 50000,
    "apply_to": "order"
}
```

**Arti**: Potongan langsung Rp 50.000 dari total

---

### 3️⃣ Gratis Produk Tertentu

```json
{
    "discount_type": "free_item",
    "free_product_id": 5,
    "free_quantity": 1
}
```

**Arti**: Gratis 1 produk ID 5 (misal: kopi gratis)

---

### 4️⃣ Buy 2 Get 1 Free (Termurah Gratis)

```json
{
    "discount_type": "free_item",
    "free_quantity": 1,
    "apply_to": "cheapest"
}
```

**Arti**: Beli 2, gratis 1 item termurah

---

### 5️⃣ Diskon untuk Kategori Tertentu

```json
{
    "discount_type": "percentage",
    "discount_value": 15,
    "apply_to": "category",
    "target_category_ids": [2, 3]
}
```

**Arti**: Diskon 15% khusus untuk kategori ID 2 dan 3

---

### 6️⃣ Diskon untuk Produk Tertentu

```json
{
    "discount_type": "percentage",
    "discount_value": 25,
    "apply_to": "products",
    "target_product_ids": [1, 5, 10]
}
```

**Arti**: Diskon 25% untuk produk ID 1, 5, dan 10

---

### 7️⃣ Gratis Ongkir

```json
{
    "discount_type": "free_shipping",
    "max_shipping_discount": 25000
}
```

**Arti**: Gratis ongkir, maksimal Rp 25.000

---

## 💡 Contoh Kasus Nyata

### Kasus 1: Grand Opening 20% Off

**Tujuan**: Diskon 20% untuk semua pembelian di atas Rp 200.000

**Form Input**:

-   Nama: "Grand Opening 20% Off"
-   Kode Kupon: "GRAND20"
-   Jadwal: 1 Des 2025 - 28 Feb 2026
-   Prioritas: 1
-   Total Limit: 1000 kali
-   Limit Per Customer: 1 kali

**Kondisi (JSON)**:

```json
{
    "min_subtotal": 200000
}
```

**Aksi (JSON)**:

```json
{
    "discount_type": "percentage",
    "discount_value": 20,
    "max_discount_amount": 100000,
    "apply_to": "order"
}
```

---

### Kasus 2: Buy 2 Get 1 Free Croissant

**Tujuan**: Beli 2 croissant gratis 1 (yang termurah gratis)

**Form Input**:

-   Nama: "Buy 2 Get 1 Free Croissant"
-   Kode Kupon: (kosongkan)
-   Promosi Publik: ON
-   Jadwal: Sekarang - 3 bulan ke depan
-   Prioritas: 2

**Kondisi (JSON)**:

```json
{
    "required_product_ids": [3],
    "min_quantity": 2
}
```

_Catatan: Asumsi ID croissant = 3_

**Aksi (JSON)**:

```json
{
    "discount_type": "free_item",
    "free_quantity": 1,
    "apply_to": "cheapest"
}
```

---

### Kasus 3: Birthday Discount 15%

**Tujuan**: Diskon 15% untuk customer yang sedang ulang tahun

**Form Input**:

-   Nama: "Birthday Special 15% Off"
-   Kode Kupon: (kosongkan)
-   Promosi Publik: ON
-   Jadwal: 1 Jan 2025 - 31 Des 2025
-   Prioritas: 3
-   Limit Per Customer: 1 kali

**Kondisi (JSON)**:

```json
{
    "is_birthday": true,
    "min_subtotal": 50000
}
```

**Aksi (JSON)**:

```json
{
    "discount_type": "percentage",
    "discount_value": 15,
    "max_discount_amount": 75000,
    "apply_to": "order"
}
```

---

### Kasus 4: Weekend Special 10% Off

**Tujuan**: Diskon 10% untuk pembelian di akhir pekan

**Form Input**:

-   Nama: "Weekend Special 10% Off"
-   Kode Kupon: "WEEKEND10"
-   Jadwal: Sekarang - 6 bulan ke depan
-   Prioritas: 4

**Kondisi (JSON)**:

```json
{
    "day_of_week": [6, 0],
    "min_subtotal": 100000
}
```

**Aksi (JSON)**:

```json
{
    "discount_type": "percentage",
    "discount_value": 10,
    "max_discount_amount": 50000,
    "apply_to": "order"
}
```

---

### Kasus 5: Gold Member Exclusive 5% Extra

**Tujuan**: Diskon tambahan 5% untuk member Gold ke atas

**Form Input**:

-   Nama: "Gold Member Exclusive 5%"
-   Kode Kupon: (kosongkan)
-   Promosi Publik: ON
-   Jadwal: 1 Jan 2025 - 31 Des 2025
-   Prioritas: 6
-   Bisa Dikombinasi: ON ✅

**Kondisi (JSON)**:

```json
{
    "customer_tier_codes": ["GOLD", "PLATINUM", "VIP"],
    "min_subtotal": 150000
}
```

**Aksi (JSON)**:

```json
{
    "discount_type": "percentage",
    "discount_value": 5,
    "max_discount_amount": 30000,
    "apply_to": "order"
}
```

---

### Kasus 6: Morning Rush - Free Coffee

**Tujuan**: Kopi gratis untuk pembelian jam 7-9 pagi

**Form Input**:

-   Nama: "Morning Rush - Free Coffee"
-   Kode Kupon: (kosongkan)
-   Promosi Publik: ON
-   Jadwal: Sekarang - 3 bulan ke depan
-   Prioritas: 7
-   Limit Per Customer: 1 kali per hari

**Kondisi (JSON)**:

```json
{
    "time_range": ["07:00", "09:00"],
    "min_subtotal": 80000
}
```

**Aksi (JSON)**:

```json
{
    "discount_type": "free_item",
    "free_product_id": 8,
    "free_quantity": 1
}
```

_Catatan: Asumsi ID kopi = 8_

---

## 🚀 Tips & Best Practices

### ✅ DO's (Yang Harus Dilakukan)

1. **Selalu set max_discount_amount untuk percentage discount**

    - Mencegah kerugian jika ada order besar
    - Contoh: 20% max Rp 100.000

2. **Gunakan prioritas yang jelas**

    - 1-3: Promosi umum
    - 4-6: Promosi segmented
    - 7-10: Promosi eksklusif

3. **Test JSON format sebelum save**

    - Gunakan online JSON validator
    - Pastikan syntax benar (kurung, koma, dll)

4. **Set usage limit untuk promosi terbatas**

    - Grand opening: 1000 total, 1 per customer
    - Flash sale: 100 total, 1 per customer

5. **Kombinasikan kondisi untuk targeting akurat**
    - Member + Waktu + Produk = Super targeted promo

### ❌ DON'Ts (Yang Harus Dihindari)

1. **Jangan lupa set end_date**

    - Promosi bisa jalan terus tanpa batas

2. **Jangan set percentage tanpa max_discount**

    - Bisa rugi besar jika ada order jutaan

3. **Jangan buat kondisi yang terlalu kompleks**

    - Customer bingung, conversion turun

4. **Jangan set prioritas yang sama untuk discount berbeda**

    - Sistem bisa random pilih yang mana

5. **Jangan lupa test dengan data real**
    - Pastikan kondisi & aksi benar-benar jalan

---

## 📊 Monitoring & Analytics

### Cara Cek Performa Discount:

1. **Buka List Discount Rules**
2. **Lihat kolom "Usage Count"**
    - Berapa kali sudah dipakai
3. **Compare dengan Usage Limit**
    - Apakah sudah habis atau masih available

### Metrics Penting:

-   **Usage Count**: Total pemakaian
-   **Conversion Rate**: % customer yang pakai discount
-   **Average Order Value**: Apakah discount meningkatkan pembelian
-   **Customer Retention**: Apakah customer repeat order

---

## 🔧 Troubleshooting

### Masalah: Discount tidak muncul di order

**Solusi**:

1. Cek `is_active` = ON
2. Cek tanggal masih berlaku
3. Cek kondisi terpenuhi (subtotal, tier, dll)
4. Cek usage limit belum habis

### Masalah: JSON Error saat save

**Solusi**:

1. Pastikan syntax JSON benar (kurung, koma)
2. Gunakan double quotes `"` bukan single quote `'`
3. Validate di https://jsonlint.com

### Masalah: Discount tidak dikombinasi

**Solusi**:

1. Cek `can_combine` = ON
2. Cek prioritas berbeda
3. Cek kondisi tidak overlap

---

## 📞 Support

Jika masih ada pertanyaan:

1. Lihat contoh discount yang sudah ada di sistem
2. Tanya tim tech support
3. Baca dokumentasi lengkap di `/docs`

---

**Last Updated**: 10 Desember 2025
**Version**: 1.0
