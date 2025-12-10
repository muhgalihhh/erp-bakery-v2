# 📊 ANALISIS SISTEM ERP BAKERY & ROADMAP PENGEMBANGAN

**Tanggal Analisis:** 10 Desember 2025  
**Versi Sistem:** v2.0  
**Framework:** Laravel 12 + FilamentPHP v3/4

---

## ✅ MODUL YANG SUDAH DIBANGUN (COMPLETE)

### 1. **FOUNDATION LAYER** ✅ (100% COMPLETE)

#### 1.1 Akuntansi & Keuangan (Accounting Core)

-   **Chart of Accounts** ✓

    -   Hierarkis parent-child structure
    -   Support multi-level (7 levels)
    -   Tipe: Asset, Liability, Equity, Revenue, Expense
    -   Subtype: current_asset, fixed_asset, cogs, operating_expense, dll
    -   Status: 100% Complete dengan data seed lengkap untuk bakery

-   **Journal Entries & Postings** ✓

    -   Double-entry bookkeeping system
    -   Polymorphic reference (referenceable)
    -   Auto-balancing validation (Debit = Credit)
    -   Status posting (Draft/Posted)
    -   User audit trail (created_by, updated_by)
    -   Status: 100% Complete dengan auto-journal dari transaksi

-   **Journal Service** ✓
    -   `JournalService` untuk centralized journal creation
    -   Auto-posting mechanism
    -   Integration dengan semua modul transaksional
    -   Status: 100% Complete & Tested

**Catatan Kritis:** Foundation ini SANGAT PENTING dan sudah solid. Semua transaksi keuangan menghasilkan journal entry otomatis.

---

### 2. **MASTER DATA** ✅ (100% COMPLETE)

#### 2.1 Product Master

-   **Products Table** ✓
    -   Tipe: raw, wip, finished, service, consumable
    -   UoM Management: purchase, stock, usage dengan conversion
    -   Flags: is_sellable, is_purchasable
    -   Stock tracking: current_stock, minimum_stock, maximum_stock
    -   Pricing: purchase_price, selling_price, standard_cost
    -   **Account Linking:** income_account_id, expense_account_id, inventory_account_id
    -   Kategori & Unit management
    -   Status: 100% Complete dengan Filament Resource

#### 2.2 Vendors (Suppliers)

-   **Vendors Table** ✓
    -   Auto-generated vendor_code
    -   Contact management
    -   Payment terms (days)
    -   Bank account details
    -   Tax ID (NPWP)
    -   Status: 100% Complete dengan Filament Resource

#### 2.3 Units & Categories

-   **Units** ✓: Satuan pengukuran (Kg, Liter, Pcs, dll)
-   **Categories** ✓: Kategori produk dengan hierarki
-   Status: 100% Complete

---

### 3. **PROCUREMENT MODULE** ✅ (95% COMPLETE)

#### 3.1 Purchase Orders

-   **Status Flow:** Draft → Pending → Approved → Partially Received → Received → Cancelled
-   **Features:**
    -   Multi-item PO dengan repeater
    -   Auto-calculate subtotal, tax (11%), discount, shipping
    -   Approval workflow dengan approval tracking
    -   Link ke Vendor dengan auto-populate data
    -   Auto-generated PO number: PO-YYYYMM-XXXX
    -   Status: 100% Complete

#### 3.2 Good Receipts (Penerimaan Barang)

-   **Features:**
    -   Link to approved PO
    -   Partial/full receipt support
    -   Auto-convert purchase UoM → stock UoM
    -   **Auto-increase product stock**
    -   **Auto-create Stock Movement (IN)**
    -   **Auto-create Journal Entry:**
        -   Debit: Persediaan Bahan Baku
        -   Credit: Hutang Usaha (Accounts Payable)
    -   Status update PO (partially_received → received)
    -   Auto-generated GR number: GR-YYYYMM-XXXX
    -   Status: 100% Complete dengan full integration

#### 3.3 Vendor Payments

-   **Payment Methods:** Cash, Bank Transfer, Check, Giro, Other
-   **Status:** Draft → Confirmed → Cancelled
-   **Features:**
    -   Link to PO (optional)
    -   Multi-currency ready
    -   **Auto-create Journal Entry saat confirmed:**
        -   Debit: Hutang Usaha (AP)
        -   Credit: Cash/Bank (based on payment method)
    -   Payment tracking per vendor
    -   Auto-generated payment number: PAY-YYYYMM-XXXX
    -   Status: 100% Complete

**Gap Analysis:**

-   ⚠️ Belum ada **Purchase Requisition** (PR - permintaan pembelian dari dept)
-   ⚠️ Belum ada **PO Approval Multi-level** (jika butuh approval bertingkat)

---

### 4. **MANUFACTURING MODULE** ✅ (90% COMPLETE)

#### 4.1 Bill of Materials (BOM) / Resep

-   **Structure:**
    -   BOM Header: product_id, quantity, description
    -   BOM Items: bahan baku + quantity + **waste_percentage**
-   **Features:**
    -   Multi-level BOM support
    -   Waste/loss tracking
    -   UoM conversion awareness
    -   Status: 100% Complete dengan Filament Resource

#### 4.2 Manufacturing Orders (Perintah Produksi)

-   **Status Flow:** Draft → Confirmed → In Progress → Done → Cancelled
-   **Features:**
    -   Link to BOM (resep)
    -   Planned qty vs Produced qty (track kegagalan produksi)
    -   Material consumption tracking
    -   **Auto-decrease bahan baku stock saat Done**
    -   **Auto-increase finished goods stock**
    -   **Auto-create Stock Movement (OUT untuk bahan, IN untuk hasil)**
    -   **Auto-create Journal Entry (Manufacturing):**
        -   Debit: Persediaan Barang Jadi
        -   Credit: Persediaan Bahan Baku (per item)
    -   Auto-generated MO number: MO-YYYYMM-XXXX
    -   Status: 90% Complete

**Gap Analysis:**

-   ⚠️ Belum ada **Production Scheduling** (jadwal produksi harian/mingguan)
-   ⚠️ Belum ada **Work Order** (jika ada multi-stage production)
-   ⚠️ Belum ada **Quality Control** (QC inspection)
-   ⚠️ Belum ada **Production Costing** detail (track labor cost, overhead per batch)

---

### 5. **INVENTORY MANAGEMENT** ✅ (85% COMPLETE)

#### 5.1 Stock Movements (History Tracking)

-   **Types:** IN, OUT, ADJUSTMENT
-   **Features:**
    -   Polymorphic reference ke source transaction (GR, MO, Sales, Adjustment)
    -   Balance tracking (balance_after)
    -   Batch/Lot tracking support (batch_number)
    -   **Expired date tracking** (crucial untuk bakery!)
    -   Warehouse support (multi-warehouse ready)
    -   Auto-created dari semua transaksi
    -   Status: 100% Complete & Integrated

#### 5.2 Stock Adjustments

-   **Types:** Stock Opname, Damaged, Expired, Lost, Found, Other
-   **Status:** Draft → Approved → Cancelled
-   **Features:**
    -   System qty vs Actual qty comparison
    -   Auto-calculate difference
    -   Reason tracking
    -   **Auto-create Stock Movement saat approved**
    -   **Auto-create Journal Entry:**
        -   If difference > 0 (Stock bertambah):
            -   Debit: Persediaan
            -   Credit: Keuntungan Lain-lain
        -   If difference < 0 (Stock berkurang):
            -   Debit: Beban Penyusutan Stok
            -   Credit: Persediaan
    -   Auto-generated adjustment number: ADJ-YYYYMM-XXXX
    -   Status: 100% Complete dengan reactive form

**Gap Analysis:**

-   ⚠️ Belum ada **Stock Movement Resource** (view-only history page)
-   ⚠️ Belum ada **Stock Validation** (prevent negative stock)
-   ⚠️ Belum ada **Low Stock Alert Widget**
-   ⚠️ Belum ada **Batch/Lot Management UI**
-   ⚠️ Belum ada **Expired Date Alert System**

---

### 6. **ROLES & PERMISSIONS** ✅ (100% COMPLETE)

-   **Package:** spatie/laravel-permission ✓
-   **Filament Shield Integration** ✓
-   **Features:**
    -   Role-based access control (RBAC)
    -   Permission per resource/action
    -   User assignment to multiple roles
    -   Status: 100% Complete

**Existing Roles (Sample):**

-   Super Admin
-   Manager
-   Accountant
-   Warehouse Staff
-   Production Staff
-   Sales/Cashier

---

## ❌ MODUL YANG BELUM ADA (MISSING CRITICAL MODULES)

Berdasarkan arsitektur ERP Bakery yang ideal, berikut modul yang **HARUS** ada namun belum dibangun:

### 🚨 PRIORITY 1: SALES & CRM MODULE (PALING PENTING!)

**Status:** ❌ BELUM ADA SAMA SEKALI!

#### Yang Dibutuhkan:

##### 7.1 Customers (Master Data)

```
Tabel: customers
- id
- customer_code (auto: CUST-XXXX)
- name
- phone (unique - kunci identifikasi)
- email
- date_of_birth (untuk diskon ulang tahun)
- address
- customer_tier_id (Silver/Gold/Platinum)
- total_points (saldo poin)
- total_spent (total pembelian - untuk auto upgrade tier)
- is_active
- notes
- created_at, updated_at, soft_deletes
```

##### 7.2 Customer Tiers (Loyalty Levels)

```
Tabel: customer_tiers
- id
- name (Silver, Gold, Platinum, VIP)
- minimum_spend (min spending untuk naik tier)
- point_multiplier (1x, 1.5x, 2x)
- discount_percentage
- benefits (JSON - deskripsi benefit)
- is_active
```

##### 7.3 Customer Points Ledger

```
Tabel: customer_points_ledger
- id
- customer_id
- type (earn, redeem, expire, adjust)
- points (positif untuk earn, negatif untuk redeem)
- referenceable_type, referenceable_id (link ke SalesOrder)
- expiry_date
- description
- created_by
- timestamps
```

##### 7.4 Discount Rules (Mesin Promo)

```
Tabel: discount_rules
- id
- name
- coupon_code (nullable)
- start_date, end_date
- priority (jika ada multiple discount)
- conditions (JSON):
  {
    "min_subtotal": 100000,
    "required_product_ids": [1,2,3],
    "customer_tier": "Gold",
    "day_of_week": ["Saturday", "Sunday"]
  }
- actions (JSON):
  {
    "type": "percent_off", // atau "amount_off", "buy_x_get_y"
    "value": 10,
    "max_amount": 50000
  }
- is_active
```

##### 7.5 Sales Orders (Penjualan)

```
Tabel: sales_orders
- id
- order_number (auto: SO-YYYYMM-XXXX)
- customer_id (nullable untuk walk-in customer)
- order_date
- order_type (pos, online, catering, preorder)
- status (draft, confirmed, in_production, ready, delivered, completed, cancelled)
- subtotal
- discount_amount
- tax_amount
- points_used (poin yang ditukar)
- points_earned
- total
- payment_status (pending, partial, paid)
- payment_method (cash, credit_card, debit, e_wallet, bank_transfer)
- served_by (user_id - kasir)
- notes
- created_at, updated_at, soft_deletes
```

##### 7.6 Sales Order Items

```
Tabel: sales_order_items
- id
- sales_order_id
- product_id
- quantity
- unit_price
- discount_amount
- tax_percentage
- total
- notes (custom request: tanpa gula, extra cream, dll)
```

##### 7.7 Sales Invoices (Optional - jika butuh invoice terpisah)

```
Untuk B2B atau catering besar, bisa jadi SO → Invoice → Payment
Untuk retail POS, SO = Invoice sekaligus
```

##### 7.8 Customer Payments (Jika support credit/installment)

```
Tabel: customer_payments
- id
- payment_number
- customer_id
- sales_order_id
- payment_date
- amount
- payment_method
- status
- notes
```

**Integrasi yang Harus Dibuat:**

1. **Sales Order Confirmed → Stock Decrease:**

    - Loop sales order items
    - Decrease product stock (finished goods)
    - Create Stock Movement (OUT)
    - Create Journal Entry:
        ```
        Debit: Kas/Piutang (tergantung payment status)
        Debit: HPP (Cost of Goods Sold)
        Credit: Pendapatan Penjualan
        Credit: Persediaan Barang Jadi
        ```

2. **Loyalty Points:**

    - Saat SO completed:
        - Calculate points: Total / 10000 \* point_multiplier
        - Create entry di customer_points_ledger (earn)
        - Create Journal Entry:
            ```
            Debit: Beban Loyalitas
            Credit: Kewajiban Poin Ditangguhkan (Liability)
            ```
    - Saat poin ditukar (redemption):
        - Decrease points
        - Create entry (redeem)
        - Create Journal Entry:
            ```
            Debit: Kewajiban Poin
            Credit: Diskon/Pendapatan
            ```

3. **Discount Engine:**
    - Service class `DiscountCalculator`
    - Jalankan saat SO dibuat
    - Pull active discount_rules
    - Evaluate JSON conditions
    - Apply actions ke SO

**Estimasi Waktu:** 2-3 Minggu (CRITICAL PRIORITY)

---

### 🚨 PRIORITY 2: POINT OF SALE (POS) UI

**Status:** ❌ BELUM ADA!

#### Yang Dibutuhkan:

-   **POS Interface** (Custom Filament Page atau standalone Vue/React)
    -   Product grid dengan kategori
    -   Shopping cart
    -   Customer search (by phone)
    -   Quick checkout
    -   Split payment support
    -   Print receipt (thermal printer support)
    -   Barcode scanner integration

**Technology Options:**

1. Filament Custom Page dengan Livewire (Full-stack Laravel)
2. Separate SPA (Vue/React) + Laravel API
3. Hybrid: Filament + TALL Stack

**Estimasi Waktu:** 2-3 Minggu

---

### 🚨 PRIORITY 3: FINANCIAL REPORTS

**Status:** ⚠️ Partial - Ada Journal Entry tapi belum ada Report UI

#### Yang Dibutuhkan:

##### 8.1 Laporan Laba Rugi (Income Statement)

-   **Period:** Monthly, Quarterly, Yearly
-   **Structure:**

    ```
    PENDAPATAN
    - Penjualan Roti           : Rp xxx
    - Penjualan Kue            : Rp xxx
    TOTAL PENDAPATAN           : Rp xxx

    HARGA POKOK PENJUALAN (HPP)
    - HPP Roti                 : (Rp xxx)
    - HPP Kue                  : (Rp xxx)
    TOTAL HPP                  : (Rp xxx)

    LABA KOTOR                 : Rp xxx

    BIAYA OPERASIONAL
    - Gaji Karyawan            : (Rp xxx)
    - Listrik & Gas            : (Rp xxx)
    - Sewa Toko                : (Rp xxx)
    TOTAL BIAYA OPERASIONAL    : (Rp xxx)

    LABA BERSIH                : Rp xxx
    ```

##### 8.2 Neraca (Balance Sheet)

-   **Components:**

    ```
    ASET
    - Aset Lancar
      - Kas                    : Rp xxx
      - Bank                   : Rp xxx
      - Persediaan             : Rp xxx
      - Piutang                : Rp xxx
    - Aset Tetap
      - Mesin & Peralatan      : Rp xxx
      - Akumulasi Penyusutan   : (Rp xxx)
    TOTAL ASET                 : Rp xxx

    KEWAJIBAN
    - Hutang Usaha             : Rp xxx
    - Kewajiban Poin Loyalitas : Rp xxx
    TOTAL KEWAJIBAN            : Rp xxx

    EKUITAS
    - Modal                    : Rp xxx
    - Laba Ditahan             : Rp xxx
    TOTAL EKUITAS              : Rp xxx
    ```

##### 8.3 Laporan Arus Kas (Cash Flow Statement)

##### 8.4 Trial Balance (Neraca Saldo)

**Implementation:**

-   Query dari `journal_postings` table
-   Group by account_id
-   Sum debit & credit
-   Format sesuai struktur laporan
-   Export to PDF/Excel

**Estimasi Waktu:** 1-2 Minggu

---

### 🔶 PRIORITY 4: INVENTORY ENHANCEMENTS

#### Yang Masih Kurang:

##### 9.1 Stock Movement Resource (View-Only)

-   Filament Table untuk lihat history
-   Filter: Product, Type, Date Range, Reference Type
-   Export Excel/PDF

##### 9.2 Stock Validation Service

-   Prevent negative stock
-   Check before MO complete
-   Check before Sales Order
-   Display error dengan saran

##### 9.3 Dashboard Widgets

-   Low Stock Alert (< minimum_stock)
-   Stock Value (total nilai inventory)
-   Fast Moving Items
-   Slow Moving Items
-   Expiring Soon (7 days alert)

##### 9.4 Batch/Lot Management UI

-   Track batch number
-   FIFO/FEFO method
-   Expired date per batch

##### 9.5 Stock Reports

-   Stock Card per Product (Kartu Stok)
-   Stock Opname Report
-   Inventory Valuation Report
-   ABC Analysis

**Estimasi Waktu:** 1 Minggu

---

### 🔶 PRIORITY 5: PRODUCTION ENHANCEMENTS

#### 10.1 Production Scheduling

-   Calendar view untuk produksi harian/mingguan
-   Drag & drop MO ke tanggal tertentu
-   Resource allocation (oven, baker)

#### 10.2 Production Costing

-   Track labor cost per MO
-   Allocate overhead (listrik, gas) per MO
-   Actual cost vs standard cost analysis

#### 10.3 Quality Control

-   QC inspection points
-   Pass/Fail/Rework status
-   Defect tracking

**Estimasi Waktu:** 2 Minggu

---

### 🔶 PRIORITY 6: HR & PAYROLL (Future)

**Status:** ❌ BELUM ADA!

#### Yang Dibutuhkan:

-   Employee Master
-   Attendance Tracking
-   Payroll Calculation
-   Salary Journal Entry
-   Leave Management

**Estimasi Waktu:** 3-4 Minggu

---

## 📋 ROADMAP PENGEMBANGAN (REKOMENDASI)

### **PHASE 1: SALES & CRM (MINGGU 1-3)** 🚨 PRIORITAS TERTINGGI!

**Mengapa Paling Penting?**

-   Tanpa Sales Module, sistem ini hanya bisa **produksi** tapi tidak bisa **menjual**!
-   Owner tidak bisa lihat **Pendapatan** dan **Laba**
-   Data keuangan tidak lengkap (hanya ada biaya, belum ada revenue)

**Target:**

1. **Minggu 1:** Database schema + Models

    - [ ] customers, customer_tiers, customer_points_ledger
    - [ ] discount_rules
    - [ ] sales_orders, sales_order_items
    - [ ] Seeder dengan sample data

2. **Minggu 2:** Filament Resources

    - [ ] CustomerResource (CRUD + points history)
    - [ ] DiscountRuleResource (JSON form untuk conditions/actions)
    - [ ] SalesOrderResource (CRUD + items repeater)
    - [ ] Integration: Stock decrease, Journal auto-post

3. **Minggu 3:** Business Logic & Services
    - [ ] DiscountCalculator service
    - [ ] LoyaltyService (earn/redeem points)
    - [ ] SalesOrder Observer (auto-stock, auto-journal)
    - [ ] Testing & refinement

---

### **PHASE 2: POS INTERFACE (MINGGU 4-5)**

1. **Minggu 4:** Basic POS

    - [ ] Product grid dengan search & category filter
    - [ ] Shopping cart (Livewire component)
    - [ ] Customer quick search (by phone)
    - [ ] Checkout flow

2. **Minggu 5:** Advanced POS
    - [ ] Barcode scanner integration
    - [ ] Split payment
    - [ ] Thermal printer receipt
    - [ ] Daily sales report (close kasir)

---

### **PHASE 3: FINANCIAL REPORTS (MINGGU 6-7)**

1. **Minggu 6:** Core Reports

    - [ ] Income Statement (Laba Rugi)
    - [ ] Balance Sheet (Neraca)
    - [ ] Trial Balance

2. **Minggu 7:** Export & UI
    - [ ] PDF Export (DomPDF)
    - [ ] Excel Export
    - [ ] Date range filter
    - [ ] Comparison (bulan ini vs bulan lalu)

---

### **PHASE 4: INVENTORY ENHANCEMENTS (MINGGU 8)**

-   [ ] Stock Movement Resource (view-only)
-   [ ] Stock Validation Service
-   [ ] Dashboard Widgets (Low Stock, Stock Value, etc)
-   [ ] Stock Card Report

---

### **PHASE 5: PRODUCTION ENHANCEMENTS (MINGGU 9-10)**

-   [ ] Production Scheduling
-   [ ] Production Costing
-   [ ] Quality Control

---

### **PHASE 6: HR & PAYROLL (MINGGU 11-14)** - OPTIONAL

-   [ ] Employee Management
-   [ ] Attendance
-   [ ] Payroll
-   [ ] Leave

---

## 🎯 KESIMPULAN & REKOMENDASI

### **Status Sistem Saat Ini:**

✅ **SANGAT KUAT DI:**

-   Foundation (Accounting, Journal)
-   Procurement (PO, GR, Payment)
-   Manufacturing (BOM, MO)
-   Inventory Tracking (Stock Movement, Adjustment)

❌ **SANGAT LEMAH/TIDAK ADA DI:**

-   **SALES & CRM** (CRITICAL GAP!)
-   POS Interface
-   Financial Reports
-   Customer Management
-   Loyalty Program

### **Rekomendasi Langkah Selanjutnya:**

**🚨 SEGERA KERJAKAN (NEXT 1-3 MINGGU):**

1. **Sales Order Module** (Week 1-2)

    - Ini HARUS jadi prioritas #1!
    - Tanpa ini, sistem tidak bisa record pendapatan
    - Accounting tidak lengkap (hanya expense, belum revenue)

2. **Customer & Loyalty** (Week 2-3)

    - Customer master data
    - Points system
    - Discount rules

3. **Basic POS** (Week 3-4)
    - Minimal bisa input penjualan
    - Tidak harus fancy, yang penting functional

**⏳ KERJAKAN SETELAH SALES SELESAI:**

4. **Financial Reports** (Week 5-6)

    - Baru ada arti setelah ada sales data
    - Laba Rugi, Neraca, Trial Balance

5. **Inventory Enhancements** (Week 7)
    - Stock widgets, validation, reports

**📅 FUTURE (AFTER 2 MONTHS):**

6. Production Enhancements
7. HR & Payroll

---

## 📦 PACKAGES YANG PERLU DITAMBAH

Untuk Sales & CRM Module:

```bash
# Sudah ada:
composer require filament/filament
composer require spatie/laravel-permission
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel

# Mungkin perlu ditambah:
composer require spatie/laravel-activitylog  # Audit trail
composer require akaunting/laravel-money     # Money formatting
composer require milon/barcode               # Barcode generation
composer require mike42/escpos-php           # Thermal printer
```

---

**NEXT ACTION:** Mulai buat Sales Order Module! Ini yang paling penting! 🚀

---

**Prepared by:** GitHub Copilot  
**Date:** 10 Desember 2025  
**Version:** 1.0
