# 🛒 Point of Sale (POS) System - User Guide

## Overview

Point of Sale (POS) system adalah antarmuka kasir yang user-friendly untuk memproses transaksi penjualan dengan cepat dan efisien. Sistem ini terintegrasi penuh dengan inventory, accounting, dan loyalty system.

## Fitur Utama

### ✅ Yang Sudah Ada

1. **Product Grid dengan Real-time Stock**

    - Grid layout 3 kolom untuk produk
    - Menampilkan gambar, nama, harga, dan stok
    - Filter produk yang bisa dijual (is_sellable & is_active)
    - Visual indicator untuk produk out of stock
    - Search produk by name atau SKU

2. **Shopping Cart Management**

    - Add to cart dengan satu klik
    - Update quantity dengan tombol +/- atau input manual
    - Remove item dari cart
    - Validasi stok real-time
    - Menampilkan subtotal per item

3. **Auto-Apply Discount Rules**

    - Otomatis mengevaluasi semua discount rules yang aktif
    - Priority-based selection (lowest priority number wins)
    - Mendukung:
        - Percentage discount (with max cap)
        - Fixed amount discount
        - Free item discount
    - Conditional checks:
        - Minimum/maximum subtotal
        - Minimum quantity
        - Customer tier requirement
        - Required products in cart
        - Day of week restriction
        - Birthday month
        - First purchase only
        - Usage limits (per customer & global)

4. **Customer Selection & Loyalty**

    - Dropdown pilihan customer
    - Walk-in customer option (no customer)
    - Menampilkan customer tier dan points
    - Auto-apply tier-based discounts
    - Auto-earn loyalty points setelah transaksi

5. **Payment Processing**

    - Multiple payment methods:
        - Cash
        - Debit Card
        - Credit Card
        - Bank Transfer
        - E-Wallet
        - QRIS
    - Auto-calculate change amount
    - Payment validation

6. **Receipt System**

    - Thermal printer compatible layout (58mm & 80mm)
    - Complete transaction details
    - Print via browser print dialog
    - Structured receipt with:
        - Store info
        - Order number & date/time
        - Items with quantity and prices
        - Subtotal, discount, tax, total
        - Payment method, paid amount, change
        - Footer message

7. **Full Integration**

    - **Inventory**: Auto-decrease stock via SalesOrderObserver
    - **Accounting**: Auto-generate journal entry (Debit: Cash/AR, Credit: Sales)
    - **Loyalty**: Auto-calculate and add points to customer
    - **Reporting**: All transactions saved in sales_orders table

8. **Keyboard Shortcuts**
    - F2: Focus search box
    - F4: Open payment modal
    - ESC: Clear cart

### ❌ Yang Belum Ada (Optional Future Enhancements)

1. Cashier shift management (opening/closing balance)
2. Multiple payment methods in one transaction
3. Partial payments
4. Order hold/resume (save cart for later)
5. Barcode scanner support
6. Quick product add by SKU entry
7. Cash drawer integration
8. Customer display screen
9. Kitchen/production order printing
10. Returns/refunds handling

## Workflow

### 1. Memulai Transaksi

1. Buka menu "Sales & CRM" → "Point of Sale"
2. Pastikan cart kosong (gunakan tombol Clear jika perlu)
3. (Optional) Pilih customer dari dropdown jika member

### 2. Memilih Produk

1. Cari produk menggunakan search box (F2)
    - Ketik nama produk atau SKU
    - Real-time filtering
2. Atau scroll grid produk
3. Klik produk card untuk add to cart
    - Produk out of stock tidak bisa diklik
    - Quantity default: 1

### 3. Mengelola Cart

1. Update quantity:
    - Klik tombol +/-
    - Atau ketik langsung di input box
    - Validasi: tidak boleh melebihi stok
2. Remove item: klik icon X merah
3. Auto-calculate subtotal setiap perubahan

### 4. Auto-Discount Application

Sistem otomatis mengevaluasi discount rules:

1. Check semua active discount rules
2. Filter rules yang memenuhi kondisi
3. Pilih discount terbaik (highest value)
4. Apply ke cart
5. Tampilkan discount name dan amount

Conditions yang dicek:

-   Subtotal minimal/maksimal
-   Quantity minimal
-   Customer tier
-   Required products
-   Day of week
-   Birthday month
-   First purchase
-   Usage limits

### 5. Payment

1. Review cart dan total
2. Klik tombol "Bayar" (F4)
3. Payment modal terbuka
4. Pilih payment method
5. Masukkan jumlah bayar
    - Auto-calculate kembalian
    - Validasi: tidak boleh kurang dari total
6. Klik "Proses Pembayaran"

### 6. Receipt & Completion

1. Receipt modal muncul
2. Review transaksi
3. Klik "Print Struk" untuk cetak
4. Klik "Transaksi Baru" untuk reset cart

### 7. Behind the Scenes (Automatic)

Setelah payment berhasil:

1. **Create SalesOrder**

    - order_type: 'pos'
    - order_channel: 'store'
    - status: 'completed'
    - payment_status: 'paid'

2. **Create SalesOrderItems**

    - Semua items dalam cart
    - Dengan quantity, price, tax

3. **SalesOrderObserver Triggered**

    - Generate order_number (SO-YYYYMMDD-XXXX)
    - Decrease product stock
    - Create journal entry:
        - Debit: Cash/Bank (payment method)
        - Credit: Sales Revenue
        - Credit: Output PPN
    - Calculate customer loyalty points
    - Update customer tier if eligible

4. **Stock Movement Recorded**
    - Type: 'sales'
    - Auto-linked to sales order

## Technical Architecture

### Files Structure

```
app/
├── Filament/
│   └── Pages/
│       └── PointOfSale.php          # Livewire component
├── Services/
│   └── DiscountEngine.php           # Discount evaluation service
└── Observers/
    └── SalesOrderObserver.php       # Auto-processing after order created

resources/
└── views/
    └── filament/
        └── pages/
            └── point-of-sale.blade.php  # POS UI
```

### Key Components

#### 1. PointOfSale.php (Livewire)

**Public Properties:**

```php
public array $cart = [];
public ?int $selectedCustomerId = null;
public float $subtotal = 0;
public float $discountAmount = 0;
public float $taxAmount = 0;
public float $total = 0;
public string $paymentMethod = 'cash';
public float $paidAmount = 0;
public float $changeAmount = 0;
public bool $showPaymentModal = false;
public bool $showReceipt = false;
```

**Key Methods:**

-   `addToCart($productId)` - Add product with validation
-   `updateQuantity($index, $quantity)` - Modify cart item
-   `removeFromCart($index)` - Delete from cart
-   `selectCustomer($customerId)` - For loyalty integration
-   `calculateTotals()` - Auto-apply discount + calculate tax
-   `openPayment()` - Show payment modal
-   `processPayment()` - Create order + trigger observers
-   `resetCart()` - Clear for next transaction

#### 2. DiscountEngine.php (Service)

**Public Methods:**

-   `findApplicableDiscounts($cartData)` - Get all eligible discounts
-   `getBestDiscount($cartData)` - Select highest priority discount
-   `calculateDiscountAmount($rule, $cartData)` - Calculate value
-   `isRuleApplicable($rule, $cartData)` - Check all conditions
-   `applyDiscount($rule, $cartData)` - Apply to cart

**Conditions Checked:**

-   Usage limits (checkUsageLimits)
-   Subtotal range (checkSubtotalRange)
-   Minimum quantity (checkMinimumQuantity)
-   Customer tier (checkCustomerTier)
-   Required products (checkRequiredProducts)
-   Day of week (checkDayOfWeek)
-   Birthday month (checkBirthdayMonth)
-   First purchase (checkFirstPurchase)

#### 3. SalesOrderObserver.php

**Events:**

-   `creating()` - Generate order_number
-   `created()` - Decrease stock, create journal, add points

### Database Integration

**Tables Used:**

-   `sales_orders` - Main transaction record
-   `sales_order_items` - Line items
-   `products` - Stock decrease
-   `stock_movements` - Stock tracking
-   `journal_entries` - Accounting
-   `journal_postings` - Double-entry details
-   `customer_loyalty_points` - Points earning

**No New Tables Required**
POS menggunakan existing tables, making it simple and maintainable.

## Troubleshooting

### Issue: Produk tidak muncul di grid

**Solution:**

-   Check `is_sellable = true`
-   Check `type = 'finished'` (bukan raw_material/wip)
-   Check `is_active = true`

### Issue: Stock tidak berkurang setelah transaksi

**Solution:**

-   Check SalesOrderObserver registered di EventServiceProvider
-   Check SalesOrder status = 'completed' (observer only trigger on completed orders)

### Issue: Journal entry tidak terbuat

**Solution:**

-   Check ChartOfAccount ada untuk:
    -   Cash (account_code: 1-10100)
    -   Sales Revenue (account_code: 4-10100)
    -   Output PPN (account_code: 2-20400)
-   Check SalesOrderObserver has createJournalEntry() method

### Issue: Discount tidak auto-apply

**Solution:**

-   Check DiscountRule `is_active = true`
-   Check DiscountRule `valid_from` <= today <= `valid_until`
-   Check conditions:
    -   min_subtotal <= cart subtotal <= max_subtotal
    -   cart quantity >= min_quantity
    -   If customer selected, check tier match
    -   If required_product_ids set, check all products in cart
    -   If day_of_week set, check current day
    -   If is_birthday_discount, check customer birthday month
    -   If is_first_purchase_only, check customer has no prior orders
    -   Check usage_limit_per_customer not exceeded
    -   Check total_usage_limit not exceeded

### Issue: Loyalty points tidak bertambah

**Solution:**

-   Check customer selected (walk-in customer tidak dapat points)
-   Check LoyaltySetting exists
-   Check SalesOrder.customer_id not null
-   Check SalesOrderObserver has addLoyaltyPoints() method

## Best Practices

### Operational

1. **Selalu pilih customer jika member** - Untuk loyalty points dan tier discounts
2. **Verify cart sebelum payment** - Pastikan quantity dan produk benar
3. **Print receipt sebagai bukti** - Untuk customer dan audit
4. **Clear cart setelah selesai** - Hindari transaksi tercampur

### Performance

1. **Limit product query** - Hanya sellable & active products
2. **Use indexes** - On products.name, products.sku for search
3. **Cache frequently accessed data** - Customer tiers, tax rates
4. **Optimize images** - Compress product images untuk fast loading

### Security

1. **Access control** - Only authorized users can access POS
2. **Transaction audit** - All sales logged with user (served_by, created_by)
3. **Stock validation** - Prevent overselling with real-time checks
4. **Payment validation** - Ensure paid_amount >= total

## Future Enhancements (Roadmap)

### Phase 1: Core Improvements (1-2 days)

-   [ ] Barcode scanner integration
-   [ ] Quick add by SKU entry field
-   [ ] Product categories filter tabs
-   [ ] Recent products quick access
-   [ ] Customer quick create from POS

### Phase 2: Advanced Features (3-4 days)

-   [ ] Cashier shift management
    -   Opening balance entry
    -   Closing balance & variance
    -   Shift sales report
    -   Multi-cashier support
-   [ ] Order hold/resume
    -   Save current cart
    -   Resume saved carts
    -   List all held orders

### Phase 3: Hardware Integration (2-3 days)

-   [ ] Thermal printer direct print (bypass browser)
-   [ ] Cash drawer auto-open
-   [ ] Customer display integration
-   [ ] Receipt printer configuration

### Phase 4: Payment Extensions (2-3 days)

-   [ ] Multiple payment methods per transaction
-   [ ] Partial payments & layaway
-   [ ] Store credit/voucher redemption
-   [ ] Payment queueing for slow connections

### Phase 5: Returns & Exchanges (3-4 days)

-   [ ] Return items workflow
-   [ ] Exchange items workflow
-   [ ] Refund processing
-   [ ] Return reason tracking
-   [ ] Reverse stock & accounting entries

## Support & Feedback

Untuk pertanyaan atau feedback tentang POS system, silakan hubungi development team atau buat issue di repository.

---

**Last Updated:** {{ current_date }}
**Version:** 1.0.0 (Simplified - No Shift Management)
