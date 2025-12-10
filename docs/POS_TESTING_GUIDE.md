# 🧪 POS System Testing Guide

## 📋 Prerequisites

### 1. Database Sudah Di-Seed

Pastikan database sudah dijalankan seeder:

```powershell
php artisan migrate:fresh --seed
```

Seeder akan membuat:

-   ✅ Super Admin user (admin@bakery.com / password)
-   ✅ Chart of Accounts (17 akun)
-   ✅ Sample Products (finished goods)
-   ✅ BOM/Recipes
-   ✅ Vendors
-   ✅ Customer Tiers (Bronze, Silver, Gold, Platinum, VIP)
-   ✅ Discount Rules (berbagai promo)
-   ✅ Sample Customers (di berbagai tier)

### 2. Laravel Server Running

```powershell
php artisan serve
```

Server berjalan di: http://127.0.0.1:8000

---

## 🔐 Login ke System

### Kredensial Default

```
Email: admin@bakery.com
Password: password
```

### Langkah Login:

1. Buka browser: **http://127.0.0.1:8000/admin**
2. Masukkan email: `admin@bakery.com`
3. Masukkan password: `password`
4. Klik **Sign in**

---

## 🛒 Cara Akses POS System

Setelah login:

1. Lihat sidebar kiri
2. Cari group **"Sales & CRM"**
3. Klik **"Point of Sale"** (icon shopping cart)
4. POS page akan terbuka

---

## 🧪 Test Scenarios

### Test 1: Basic Transaction (Walk-in Customer)

**Tujuan:** Test transaksi dasar tanpa customer member

**Steps:**

1. Buka POS page
2. Customer dropdown: biarkan "Walk-in Customer" (default)
3. Cari produk: ketik nama produk di search box
4. Klik produk untuk add to cart
5. Verify:
    - ✅ Produk masuk ke cart
    - ✅ Quantity = 1
    - ✅ Subtotal muncul
6. Update quantity: klik tombol `+` atau `-`
7. Verify quantity berubah
8. Klik tombol **"Bayar"** (atau tekan F4)
9. Payment modal terbuka
10. Verify:
    - ✅ Total displayed
    - ✅ Payment method = Cash (default)
    - ✅ Paid amount = Total (auto-filled)
11. Ubah paid amount jadi lebih besar
12. Verify kembalian auto-calculate
13. Klik **"Proses Pembayaran"**
14. Wait for notification
15. Receipt modal terbuka
16. Verify:
    - ✅ Order number generated (SO-20251210-XXXX)
    - ✅ Items listed correctly
    - ✅ Totals match
    - ✅ Payment info correct
17. Klik **"Print Struk"** → browser print dialog
18. Klik **"Transaksi Baru"** → cart cleared

**Expected Results:**

-   ✅ Transaction successful
-   ✅ No customer loyalty points (walk-in)
-   ✅ Stock decreased (check product stock)
-   ✅ Journal entry created (check Jurnal Umum)
-   ✅ Sales order recorded (check Sales Orders)

---

### Test 2: Member Transaction dengan Loyalty Points

**Tujuan:** Test dengan customer member untuk earn points

**Setup:**

-   Pastikan ada customer di database (dari SampleCustomerSeeder)
-   Check: Menu "Sales & CRM" → "Customers"

**Steps:**

1. Buka POS page
2. Customer dropdown: pilih customer (misalnya "Budi Santoso - Bronze")
3. Verify:
    - ✅ Customer info displayed
    - ✅ Tier shown (e.g., "Bronze")
    - ✅ Current points shown
4. Add produk ke cart (minimal Rp 50,000 subtotal)
5. Klik **"Bayar"**
6. Process payment
7. Setelah transaksi selesai, klik **"Transaksi Baru"**
8. **Verify Loyalty:**
    - Go to "Sales & CRM" → "Customers"
    - Find customer yang tadi dipilih
    - Click customer name → view detail
    - Check "Loyalty Points" tab
    - Verify:
        - ✅ Points bertambah
        - ✅ Transaction tercatat di history
        - ✅ Tier mungkin upgrade (jika threshold tercapai)

**Expected Results:**

-   ✅ Customer earned points (e.g., 1 point per Rp 10,000)
-   ✅ Points visible in customer detail
-   ✅ Transaction linked to customer
-   ✅ Tier upgraded if total points >= threshold

---

### Test 3: Auto-Apply Discount Rule

**Tujuan:** Test discount rules auto-apply

**Setup:**

-   Check existing discount rules: "Sales & CRM" → "Discount Rules"
-   Note conditions (e.g., min subtotal Rp 100,000 → 10% off)

**Steps:**

1. Buka POS page
2. Add products until subtotal < minimum (e.g., Rp 80,000)
3. Verify:
    - ✅ No discount applied
    - ✅ Discount amount = 0
4. Add more products until subtotal >= minimum (e.g., Rp 120,000)
5. Verify:
    - ✅ Discount auto-applied
    - ✅ Discount name displayed (e.g., "Belanja Min 100rb")
    - ✅ Discount amount shown (e.g., -Rp 12,000)
    - ✅ Total updated correctly
6. Select customer with specific tier (e.g., "Gold")
7. Verify:
    - ✅ Tier-based discount applied if rule exists
    - ✅ Best discount selected (highest value)
8. Process payment
9. Verify discount recorded in sales order

**Expected Results:**

-   ✅ Discount auto-evaluated every cart change
-   ✅ Best discount selected (priority-based)
-   ✅ Discount amount deducted from total
-   ✅ Discount source recorded (which rule)

---

### Test 4: Stock Validation

**Tujuan:** Test stock limit enforcement

**Setup:**

-   Check product stock: "Inventory" → "Products"
-   Note current stock (e.g., Product A has 10 units)

**Steps:**

1. Buka POS page
2. Add Product A to cart
3. Increase quantity to max stock (e.g., 10)
4. Verify:
    - ✅ Quantity accepts up to stock limit
5. Try to increase beyond stock (click `+` button or type manually)
6. Verify:
    - ✅ Validation error/warning
    - ✅ Quantity tidak melebihi stock
7. Process payment successfully
8. **Verify Stock Decrease:**
    - Go to "Inventory" → "Products"
    - Find Product A
    - Verify:
        - ✅ Current stock decreased by sold quantity
9. **Verify Stock Movement:**
    - Go to "Inventory" → "Stock Movements"
    - Find latest movement
    - Verify:
        - ✅ Type = "sales"
        - ✅ Quantity = negative (decrease)
        - ✅ Reference = SO number
        - ✅ After stock = updated stock

**Expected Results:**

-   ✅ Cannot exceed available stock
-   ✅ Stock decreased after transaction
-   ✅ Stock movement recorded
-   ✅ Accurate inventory tracking

---

### Test 5: Accounting Integration

**Tujuan:** Verify journal entry auto-creation

**Steps:**

1. Note current chart of accounts balances:
    - "Accounting" → "Chart of Accounts"
    - Note: Cash (1-10100), Sales Revenue (4-10100), Output PPN (2-20400)
2. Perform POS transaction (e.g., Rp 100,000 + tax)
3. After transaction, go to "Accounting" → "Jurnal Umum"
4. Find latest journal entry
5. Verify:
    - ✅ Transaction number = SO number
    - ✅ Date = today
    - ✅ Description includes order number
6. Click journal entry → view postings
7. Verify double-entry:
    - ✅ **Debit:** Cash (1-10100) = Rp 111,000 (total with tax)
    - ✅ **Credit:** Sales Revenue (4-10100) = Rp 100,000 (subtotal - discount)
    - ✅ **Credit:** Output PPN (2-20400) = Rp 11,000 (11% tax)
    - ✅ Total Debit = Total Credit
8. Go back to "Chart of Accounts"
9. Verify:
    - ✅ Cash balance increased
    - ✅ Sales Revenue balance increased
    - ✅ Output PPN balance increased

**Expected Results:**

-   ✅ Journal entry created automatically
-   ✅ Correct accounts debited/credited
-   ✅ Amounts match transaction
-   ✅ Balanced double-entry (debit = credit)
-   ✅ Account balances updated

---

### Test 6: Multiple Payment Methods

**Tujuan:** Test berbagai metode pembayaran

**Payment Methods to Test:**

1. Cash
2. Debit Card
3. Credit Card
4. Bank Transfer
5. E-Wallet
6. QRIS

**Steps for Each Method:**

1. Add products to cart
2. Click "Bayar"
3. Select payment method from dropdown
4. Enter paid amount
5. Process payment
6. Verify:
    - ✅ Payment method recorded in sales order
    - ✅ Journal entry debits correct account:
        - Cash → Cash account
        - Others → Bank/Receivable account

**Expected Results:**

-   ✅ All payment methods selectable
-   ✅ Payment method saved correctly
-   ✅ Displayed on receipt
-   ✅ Different accounts used in journal entry

---

### Test 7: Keyboard Shortcuts

**Tujuan:** Test efficiency features

**Shortcuts to Test:**

1. **F2 - Focus Search**

    - Press F2
    - Verify: cursor in search box
    - Type product name
    - Verify: results filter

2. **F4 - Open Payment**

    - Add products to cart
    - Press F4
    - Verify: payment modal opens

3. **ESC - Clear Cart**
    - Add products to cart
    - Press ESC
    - Verify: cart cleared, back to empty state

**Expected Results:**

-   ✅ All shortcuts work as intended
-   ✅ Faster workflow for experienced cashiers

---

### Test 8: Edge Cases

#### 8.1 Empty Cart Payment

1. Don't add any products
2. Click "Bayar" button
3. Verify:
    - ✅ Warning message: "Keranjang kosong"
    - ✅ Payment modal tidak terbuka

#### 8.2 Insufficient Payment

1. Add products (total Rp 100,000)
2. Click "Bayar"
3. Enter Rp 50,000 in paid amount
4. Try to click "Proses Pembayaran"
5. Verify:
    - ✅ Button disabled
    - ✅ Cannot proceed
    - ✅ Validation error

#### 8.3 Out of Stock Product

1. Find product with 0 stock
2. Try to click the product card
3. Verify:
    - ✅ Button disabled
    - ✅ "Habis" badge shown
    - ✅ Cannot add to cart

#### 8.4 Discount with Unmet Conditions

1. Create discount rule: Min subtotal Rp 200,000
2. Add products total Rp 150,000
3. Verify:
    - ✅ Discount not applied
    - ✅ Discount amount = 0
4. Add more to reach Rp 200,000
5. Verify:
    - ✅ Discount now applied

#### 8.5 Multiple Applicable Discounts

1. Ensure multiple discount rules active:
    - Rule A: 10% off, priority 1
    - Rule B: Rp 20,000 off, priority 2
2. Cart meets both conditions
3. Verify:
    - ✅ Only Rule A applied (lower priority number)
    - ✅ Higher value discount selected

**Expected Results:**

-   ✅ All edge cases handled gracefully
-   ✅ User-friendly error messages
-   ✅ No crashes or unexpected behavior

---

### Test 9: Receipt Printing

**Tujuan:** Test receipt generation & printing

**Steps:**

1. Complete a transaction
2. Receipt modal opens
3. Review receipt content:
    - ✅ Store name
    - ✅ Date & time
    - ✅ Order number (SO-YYYYMMDD-XXXX)
    - ✅ Customer name (if selected)
    - ✅ Items table with qty × price
    - ✅ Subtotal, discount, tax, total
    - ✅ Payment method, paid, change
    - ✅ Footer message
4. Click "Print Struk"
5. Browser print dialog opens
6. Print preview shows:
    - ✅ Thermal format (narrow width)
    - ✅ Monospace font
    - ✅ All transaction details
7. (Optional) Print to PDF or actual printer
8. Verify printed receipt matches transaction

**Expected Results:**

-   ✅ Receipt formatted correctly
-   ✅ All information present
-   ✅ Thermal printer compatible
-   ✅ Professional appearance

---

### Test 10: Consecutive Transactions

**Tujuan:** Test multiple transactions in sequence

**Steps:**

1. Complete Transaction 1
2. Click "Transaksi Baru"
3. Verify:
    - ✅ Cart cleared
    - ✅ Customer reset to "Walk-in"
    - ✅ Totals reset to 0
    - ✅ Ready for next transaction
4. Add different products
5. Complete Transaction 2
6. Verify:
    - ✅ New order number (incremented)
    - ✅ Independent from Transaction 1
    - ✅ Correct stock decrease
7. Repeat for Transaction 3, 4, 5
8. Go to "Sales & CRM" → "Sales Orders"
9. Verify:
    - ✅ All 5 transactions listed
    - ✅ All order_type = 'pos'
    - ✅ All status = 'completed'
    - ✅ Sequential order numbers

**Expected Results:**

-   ✅ System handles rapid consecutive transactions
-   ✅ No data mixing between transactions
-   ✅ Each transaction independent
-   ✅ Order numbers increment correctly

---

## 📊 Verification Checklist

### After Testing, Verify:

#### Database Tables

-   [ ] `sales_orders` - Transactions recorded
-   [ ] `sales_order_items` - Line items correct
-   [ ] `products` - Stock decreased
-   [ ] `stock_movements` - Movements logged
-   [ ] `journal_entries` - Journal created
-   [ ] `journal_postings` - Postings balanced
-   [ ] `customer_loyalty_points` - Points added (for members)

#### Filament Admin

-   [ ] Sales Orders list shows POS transactions
-   [ ] Order details display correctly
-   [ ] Customer points updated
-   [ ] Chart of Accounts balances correct
-   [ ] Stock movements visible
-   [ ] Journal entries balanced

#### UI/UX

-   [ ] Product grid loads fast
-   [ ] Search works instantly
-   [ ] Cart updates reactive
-   [ ] Modals open/close smoothly
-   [ ] Notifications appear
-   [ ] No console errors
-   [ ] Dark mode works (if using)

---

## 🐛 Common Issues & Solutions

### Issue 1: Cannot Login

**Symptoms:** Invalid credentials error

**Solutions:**

```powershell
# Re-run seeder
php artisan db:seed --class=SuperAdminSeeder

# Or migrate fresh
php artisan migrate:fresh --seed
```

### Issue 2: POS Page Not Showing

**Symptoms:** Menu item not visible

**Solutions:**

-   Check user has permission (Super Admin should have all)
-   Clear cache: `php artisan optimize:clear`
-   Check navigation group name matches

### Issue 3: Products Not Loading

**Symptoms:** Empty grid or "Tidak ada produk"

**Solutions:**

-   Run ProductSeeder: `php artisan db:seed --class=ProductSeeder`
-   Check products table: must have `is_sellable=1`, `type='finished'`, `is_active=1`
-   Check in Filament: "Inventory" → "Products"

### Issue 4: Discount Not Applying

**Symptoms:** Discount amount = 0 despite meeting conditions

**Solutions:**

-   Check discount rules: "Sales & CRM" → "Discount Rules"
-   Verify rule `is_active = true`
-   Check `valid_from` and `valid_until` dates
-   Verify conditions met (subtotal, quantity, etc.)
-   Check DiscountEngine service logs

### Issue 5: Stock Not Decreasing

**Symptoms:** Product stock unchanged after sale

**Solutions:**

-   Verify SalesOrderObserver registered
-   Check EventServiceProvider:
    ```php
    protected $observers = [
        SalesOrder::class => [SalesOrderObserver::class],
    ];
    ```
-   Check order status = 'completed' (observer only triggers on completed)
-   Clear cache: `php artisan config:clear`

### Issue 6: Journal Entry Not Created

**Symptoms:** No journal entry after transaction

**Solutions:**

-   Check ChartOfAccount records exist:
    -   Cash (1-10100)
    -   Sales Revenue (4-10100)
    -   Output PPN (2-20400)
-   Run ChartOfAccountSeederSimple
-   Check SalesOrderObserver `createJournalEntry()` method
-   Look for errors in `storage/logs/laravel.log`

### Issue 7: Loyalty Points Not Added

**Symptoms:** Customer points unchanged

**Solutions:**

-   Ensure customer selected (not walk-in)
-   Check LoyaltySetting exists in database
-   Verify SalesOrder.customer_id not null
-   Check SalesOrderObserver `addLoyaltyPoints()` method
-   View customer detail: "Sales & CRM" → "Customers" → click name

---

## 📈 Performance Testing

### Load Test Recommendations

1. **Concurrent Users:**

    - Test 5-10 cashiers using POS simultaneously
    - Monitor server response time
    - Check for database locks

2. **Large Cart:**

    - Add 50+ items to cart
    - Verify performance stays smooth
    - Check calculation speed

3. **Many Products:**

    - Seed 500+ products
    - Test search performance
    - Verify grid pagination

4. **Database Size:**
    - After 1000+ transactions
    - Check query performance
    - Consider indexes on frequently queried columns

---

## ✅ Testing Complete Checklist

-   [ ] Login successful with default credentials
-   [ ] POS page accessible from navigation
-   [ ] Product grid displays correctly
-   [ ] Search filters products real-time
-   [ ] Add to cart works
-   [ ] Update quantity works
-   [ ] Remove from cart works
-   [ ] Customer selection works
-   [ ] Discount auto-applies correctly
-   [ ] Payment modal opens
-   [ ] Change calculation accurate
-   [ ] Payment processing succeeds
-   [ ] Receipt displays correctly
-   [ ] Print function works
-   [ ] Stock decreases after sale
-   [ ] Journal entry created
-   [ ] Loyalty points added (for members)
-   [ ] Clear cart works
-   [ ] Consecutive transactions work
-   [ ] Keyboard shortcuts functional
-   [ ] Edge cases handled gracefully
-   [ ] No console errors
-   [ ] No database errors

---

## 📝 Test Report Template

```
POS System Test Report
Date: [DATE]
Tester: [NAME]
Environment: [local/staging/production]

Test Results:
✅ PASSED: [count]
❌ FAILED: [count]
⚠️ WARNING: [count]

Details:
1. Basic Transaction: [PASS/FAIL]
   - Notes: [any observations]

2. Member Transaction: [PASS/FAIL]
   - Notes: [any observations]

3. Discount Rules: [PASS/FAIL]
   - Notes: [any observations]

[... continue for all tests ...]

Issues Found:
1. [Description]
   - Severity: [High/Medium/Low]
   - Steps to reproduce: [...]
   - Expected: [...]
   - Actual: [...]

Recommendations:
- [Improvement suggestions]

Overall Assessment: [PASS/FAIL]
Ready for Production: [YES/NO]
```

---

## 🎓 Next Steps After Testing

1. **If All Tests Pass:**

    - ✅ Mark POS system as production-ready
    - Train cashier staff
    - Deploy to production
    - Monitor for 1-2 weeks
    - Collect user feedback

2. **If Issues Found:**

    - Document all bugs
    - Prioritize by severity
    - Fix critical issues first
    - Re-test after fixes
    - Repeat until all tests pass

3. **Continuous Improvement:**
    - Collect cashier feedback
    - Identify pain points
    - Plan enhancements
    - Iterate in sprints

---

**Testing Guide Version:** 1.0  
**Last Updated:** December 10, 2025  
**Status:** Ready for Use
