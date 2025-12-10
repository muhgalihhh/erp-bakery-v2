# 🎯 Quick Start: Cara Membuat Discount/Promosi

## 🚀 Langkah Cepat

### 1. **Buka Menu Discount Rules**

-   Login ke admin panel (http://127.0.0.1:8000/admin)
-   Email: `admin@bakery.com`
-   Password: `password`
-   Klik menu **💰 Sales & CRM** → **Discount Rules**
-   Klik tombol **Create**

### 2. **Contoh Promosi Sederhana**

#### ✅ Diskon 20% untuk Pembelian di Atas Rp 200.000

**Isi Form**:

-   Nama: `Grand Opening 20% Off`
-   Kode Kupon: `GRAND20`
-   Mulai: Sekarang
-   Berakhir: 3 bulan ke depan
-   Prioritas: `5`

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

## 📚 Dokumentasi Lengkap

Lihat file: **[DISCOUNT_RULES_GUIDE.md](./DISCOUNT_RULES_GUIDE.md)**

Di sana ada:

-   ✅ 20+ contoh kasus nyata (Buy 2 Get 1, Birthday Discount, Weekend Special, dll)
-   ✅ Penjelasan lengkap setiap field JSON
-   ✅ Tips & best practices
-   ✅ Troubleshooting common issues

---

## 🎓 Video Tutorial (Coming Soon)

-   [ ] Basic: Membuat Diskon Persentase
-   [ ] Intermediate: Buy X Get Y Free
-   [ ] Advanced: Kombinasi Multiple Conditions

---

## 💡 Tips Penting

1. **Selalu set `max_discount_amount`** untuk percentage discount
2. **Test JSON format** di https://jsonlint.com sebelum save
3. **Set usage limit** untuk promosi terbatas
4. **Gunakan prioritas** yang jelas (1-10 scale)
5. **Aktifkan toggle** "is_active" agar promosi langsung jalan

---

## 🆘 Butuh Bantuan?

1. Lihat **Panduan di Form** (ada collapse guide untuk Conditions & Actions)
2. Baca **[DISCOUNT_RULES_GUIDE.md](./DISCOUNT_RULES_GUIDE.md)**
3. Lihat **Sample Data** yang sudah ada di sistem (7 discount rules contoh)
4. Hubungi tim support

---

**Happy Promoting!** 🎉
