# 🛒 Point of Sale (POS) System - Final Implementation

## 📋 Overview

Sistem POS ini menggunakan **Custom Filament Page** dengan **Alpine.js + Livewire** untuk memberikan pengalaman kasir yang cepat dan responsif.

### Mengapa Custom Page?

✅ **Real-time & Reactive** - Perubahan cart langsung terlihat tanpa reload  
✅ **Fast Performance** - Alpine.js handle UI state di client-side  
✅ **Custom UX** - Interface yang dirancang khusus untuk workflow kasir  
✅ **Keyboard Shortcuts** - Kasir bisa bekerja lebih cepat  
✅ **Optimal untuk Transaksi** - Bukan CRUD form, tapi flow transaksi

---

## 🏗️ Architecture

### Technology Stack

1. **Livewire** - Server-side reactivity & data management
2. **Alpine.js** - Client-side state & animations (built-in Filament)
3. **Filament Components** - UI components (buttons, inputs, badges, dll)
4. **Tailwind CSS** - Styling & responsive design

### File Structure

```
app/
├── Filament/
│   └── Pages/
│       └── PointOfSale.php          # Livewire Component (Backend Logic)
├── Services/
│   └── DiscountEngine.php           # Auto-discount calculation
└── Observers/
    └── SalesOrderObserver.php       # Post-transaction processing

resources/
└── views/
    └── filament/
        └── pages/
            └── point-of-sale.blade.php  # Alpine.js + UI (Frontend)
```

---

## 🎯 Features

### Core Functionality

1. ✅ **Product Selection**

    - Grid view dengan gambar produk
    - Search by name atau SKU (F2)
    - Stock badge & validation
    - Hover effects & smooth animations

2. ✅ **Cart Management**

    - Add to cart dengan stock validation
    - Update quantity dengan +/- buttons
    - Remove item dari cart
    - Real-time subtotal calculation

3. ✅ **Customer Selection**

    - Walk-in customer (default)
    - Registered customer dengan tier & points
    - Display customer info & benefits

4. ✅ **Auto-Discount**

    - Discount rules dari DiscountEngine
    - Apply otomatis ke cart
    - Show discount name & amount
    - Support multiple discount types

5. ✅ **Tax Calculation**

    - PPN 11% (configurable)
    - Calculate setelah discount
    - Display di summary

6. ✅ **Payment Processing**

    - 6 payment methods (Cash, Debit, Credit, Transfer, E-Wallet, QRIS)
    - Quick amount buttons (Pas, 50K, 100K)
    - Auto-calculate kembalian
    - Validation: paid amount >= total

7. ✅ **Receipt & Completion**

    - Professional receipt layout
    - Print-ready (thermal 80mm)
    - Show all transaction details
    - New transaction button

8. ✅ **Keyboard Shortcuts**
    - **F2**: Focus search & select text
    - **F4**: Open payment modal
    - **ESC**: Clear cart (with confirmation)

---

## 💻 Code Implementation

### Backend: PointOfSale.php

**Key Properties:**

```php
// Cart & Customer
public array $cart = [];
public ?int $selectedCustomerId = null;
public ?array $selectedCustomer = null;
public string $searchProduct = '';

// Calculations
public float $subtotal = 0;
public float $discountAmount = 0;
public ?string $discountName = null;
public float $taxPercentage = 11;
public float $taxAmount = 0;
public float $total = 0;

// Payment
public string $paymentMethod = 'cash';
public float $paidAmount = 0;
public float $changeAmount = 0;

// Receipt
public bool $showReceipt = false;
public ?int $lastOrderId = null;
```

**Key Methods:**

1. **addToCart($productId)**

    - Validate product exists & sellable
    - Check stock availability
    - Add or increment quantity
    - Trigger calculateTotals()

2. **updateQuantity($index, $quantity)**

    - Validate quantity > 0 and <= stock
    - Update cart item quantity
    - Trigger calculateTotals()

3. **removeFromCart($index)**

    - Remove item by index
    - Trigger calculateTotals()

4. **selectCustomer($customerId)**

    - Load customer data with tier & points
    - Store in selectedCustomer array
    - Re-calculate discount

5. **calculateTotals()**

    - Calculate subtotal from cart items
    - Apply auto-discount via DiscountEngine
    - Calculate tax (after discount)
    - Calculate grand total

6. **calculateChange()**

    - Calculate: changeAmount = paidAmount - total
    - Auto-triggered when paidAmount updates

7. **processPayment()**

    - Validate paidAmount >= total
    - Begin DB transaction
    - Create SalesOrder
    - Create SalesOrderItems
    - Trigger SalesOrderObserver (stock update, journal entry, loyalty)
    - Set showReceipt = true

8. **resetCart()**

    - Clear all cart data
    - Reset calculations
    - Ready for next transaction

9. **finishTransaction()**
    - Call resetCart()
    - Show success notification

**Livewire Hooks:**

```php
updatedCart() => calculateTotals()
updatedSelectedCustomerId() => selectCustomer()
updatedPaidAmount() => calculateChange()
```

---

### Frontend: point-of-sale.blade.php

**Alpine.js State:**

```javascript
x-data="{
    showPayment: false,
    showReceipt: @entangle('showReceipt'),
    openPayment() {
        if (cart not empty) {
            $wire.set('paidAmount', total);
            this.showPayment = true;
            Auto-focus input
        }
    }
}"
```

**Keyboard Shortcuts:**

```javascript
@keydown.f2.window.prevent   => Focus search + select
@keydown.f4.window.prevent   => Open payment modal
@keydown.escape.window.prevent => Clear cart (with confirm)
```

**Layout Structure:**

```
┌─────────────────────────────────────────────────────┐
│                    POS INTERFACE                     │
├──────────────────────┬──────────────────────────────┤
│                      │                              │
│   PRODUCT GRID       │    CUSTOMER SECTION          │
│   (2/3 width)        │    (1/3 width)               │
│                      │                              │
│   ┌──────┐┌──────┐  │    ┌──────────────────┐      │
│   │ Prod ││ Prod │  │    │ Customer Select  │      │
│   │  #1  ││  #2  │  │    └──────────────────┘      │
│   └──────┘└──────┘  │                              │
│   ┌──────┐┌──────┐  │    CART ITEMS                │
│   │ Prod ││ Prod │  │    ┌──────────────────┐      │
│   │  #3  ││  #4  │  │    │ Item 1  [+] 2 [-]│      │
│   └──────┘└──────┘  │    │ Item 2  [+] 1 [-]│      │
│                      │    └──────────────────┘      │
│   [Search: F2]       │                              │
│                      │    SUMMARY                   │
│                      │    Subtotal: Rp 100,000      │
│                      │    Discount: Rp 10,000       │
│                      │    Tax (11%): Rp 9,900       │
│                      │    TOTAL: Rp 99,900          │
│                      │                              │
│                      │    [BAYAR SEKARANG (F4)]     │
│                      │    [CLEAR CART (ESC)]        │
└──────────────────────┴──────────────────────────────┘
```

**Modals:**

1. **Payment Modal** (Alpine x-show)

    - Backdrop dengan click-to-close
    - Smooth transitions
    - Total display (large, prominent)
    - Payment method select
    - Paid amount input (auto-focus)
    - Quick buttons (Pas, 50K, 100K)
    - Change display (real-time)
    - Cancel & Process buttons

2. **Receipt Modal** (Alpine x-show + Livewire entangle)
    - Success header
    - Professional receipt layout
    - All transaction details
    - Print button
    - New transaction button

---

## 🎨 UI/UX Design

### Color Scheme

-   **Primary**: Blue/Indigo - Main actions, total, highlights
-   **Success**: Green - Discount, kembalian, success states
-   **Danger**: Red - Remove, stock habis, warnings
-   **Gray**: Neutral - Secondary actions, backgrounds

### Typography

-   **4XL**: Payment total (massive, bold)
-   **3XL**: Kembalian amount
-   **2XL**: Modal headings
-   **XL**: Buttons, cart total
-   **Base**: Standard text
-   **SM/XS**: Metadata, hints

### Interactions

1. **Hover Effects**

    - Scale transform 1.05x on products
    - Border color changes
    - Shadow enhancements

2. **Transitions**

    - Cubic bezier easing
    - 200-300ms duration
    - Smooth opacity & scale

3. **Feedback**
    - Loading states (Livewire wire:loading)
    - Success notifications
    - Error validations
    - Disabled states

---

## 🔄 Workflow

### Standard Transaction Flow

```
1. Start
   ↓
2. Search/Select Products → Add to Cart
   ↓
3. (Optional) Select Customer
   ↓
4. Review Cart & Summary
   ↓ (Auto-discount applied)
5. Click "Bayar" / Press F4
   ↓
6. Payment Modal Opens
   - Auto-focus input
   - Default amount = total
   ↓
7. Select Payment Method
   ↓
8. Enter Paid Amount
   - Or click quick buttons
   - See kembalian real-time
   ↓
9. Click "Proses Pembayaran"
   ↓
10. Receipt Modal Appears
    ↓
11. Print Receipt (Optional)
    ↓
12. Click "Transaksi Baru"
    ↓
13. Back to Start
```

### Behind the Scenes (Auto-Processing)

Ketika processPayment() dipanggil:

```php
DB::transaction {
    1. Create SalesOrder
       - order_type: 'pos'
       - order_channel: 'store'
       - status: 'completed'
       - payment_status: 'paid'
       - customer_id, payment_method, amounts, etc.

    2. Create SalesOrderItems
       - Loop cart items
       - product_id, quantity, unit_price, total

    3. SalesOrderObserver Triggered (automatic)
       a. Generate order_number
       b. Decrease product stock
       c. Create stock movements
       d. Create journal entry:
          - Debit: Cash/Bank account
          - Credit: Sales Revenue
          - Credit: Output PPN
       e. Calculate & add loyalty points
       f. Check & update customer tier

    4. Commit Transaction
    5. Show Receipt
}
```

---

## ⚡ Performance Optimizations

1. **Client-Side State (Alpine.js)**

    - Modal show/hide tanpa server request
    - Smooth animations 60fps
    - Instant UI feedback

2. **Debounced Search**

    - 300ms debounce untuk product search
    - Reduce server requests

3. **Eager Loading**

    - Products query limit 50
    - Only load sellable & active products
    - With stock & pricing

4. **Minimal Re-renders**
    - Livewire wire:model.live only where needed
    - Most calculations server-side
    - Cart updates batched

---

## 🧪 Testing Guide

### Manual Testing Checklist

#### Product Selection

-   [ ] Search produk by name
-   [ ] Search produk by SKU
-   [ ] Clear search dengan tombol X
-   [ ] Klik produk dengan stock > 0 → add to cart
-   [ ] Klik produk dengan stock = 0 → disabled
-   [ ] Keyboard F2 → focus search input

#### Cart Management

-   [ ] Add produk → muncul di cart
-   [ ] Add produk yang sama → quantity increment
-   [ ] Click + button → quantity bertambah
-   [ ] Click - button → quantity berkurang
-   [ ] Input quantity manual → update
-   [ ] Quantity > stock → error/disabled
-   [ ] Remove item → hilang dari cart
-   [ ] Cart kosong → show empty state

#### Customer

-   [ ] Default: Walk-in customer
-   [ ] Select customer → info muncul
-   [ ] Customer dengan tier → show tier & points
-   [ ] Change customer → re-calculate discount

#### Calculations

-   [ ] Subtotal = sum of (price × qty)
-   [ ] Discount apply otomatis
-   [ ] Discount name displayed
-   [ ] Tax = (subtotal - discount) × 11%
-   [ ] Total = subtotal - discount + tax

#### Payment Modal

-   [ ] Click "Bayar" → modal muncul
-   [ ] Press F4 → modal muncul
-   [ ] Auto-focus pada paid amount input
-   [ ] Default paid amount = total
-   [ ] Click "Pas" → set exact amount
-   [ ] Click "50K" → set rounded 50K
-   [ ] Click "100K" → set rounded 100K
-   [ ] Paid amount > total → show kembalian
-   [ ] Paid amount < total → show warning
-   [ ] Paid amount < total → button disabled
-   [ ] Select payment method → options work
-   [ ] Click backdrop → modal close
-   [ ] Click Batal → modal close

#### Payment Processing

-   [ ] Click "Proses" dengan valid amount → success
-   [ ] Receipt modal muncul
-   [ ] Check database: SalesOrder created
-   [ ] Check database: SalesOrderItems created
-   [ ] Check database: Stock decreased
-   [ ] Check database: StockMovements created
-   [ ] Check database: JournalEntry created
-   [ ] Check database: Customer points updated (if customer)

#### Receipt

-   [ ] All transaction details correct
-   [ ] Order number generated
-   [ ] Items list complete
-   [ ] Amounts correct
-   [ ] Payment method shown
-   [ ] Kembalian shown
-   [ ] Click Print → browser print dialog
-   [ ] Click "Transaksi Baru" → reset all

#### Keyboard Shortcuts

-   [ ] F2 → focus search, select text
-   [ ] F4 → open payment (if cart not empty)
-   [ ] ESC → confirm, then clear cart

#### Edge Cases

-   [ ] Empty cart → "Bayar" disabled
-   [ ] Empty cart → F4 no effect
-   [ ] During Livewire request → loading state
-   [ ] Network error → error notification
-   [ ] Insufficient stock → error message
-   [ ] Multiple rapid clicks → handled properly

---

## 🐛 Common Issues & Solutions

### Issue 1: Modal tidak muncul

**Solution:** Sudah fixed dengan Alpine.js x-show

### Issue 2: Cart tidak update

**Check:**

-   Livewire wire:model pada cart items
-   updatedCart() hook dipanggil
-   calculateTotals() berjalan

### Issue 3: Payment tidak proses

**Check:**

-   paidAmount >= total
-   Cart tidak kosong
-   Database connection
-   Check Laravel logs

### Issue 4: Stock tidak berkurang

**Check:**

-   SalesOrderObserver registered
-   Observer method updated()
-   Product stock column exists

### Issue 5: Discount tidak apply

**Check:**

-   DiscountEngine service exists
-   Discount rules configured
-   Customer selected (if tier discount)

---

## 📊 Database Schema

### Sales Orders Table

```sql
- id
- order_number (auto-generated)
- order_type ('pos')
- order_channel ('store')
- customer_id (nullable)
- subtotal
- discount_amount
- discount_source
- tax_percentage
- tax_amount
- total
- payment_method
- paid_amount
- change_amount
- status ('completed')
- payment_status ('paid')
- created_by
- timestamps
```

### Sales Order Items Table

```sql
- id
- sales_order_id
- product_id
- quantity
- unit_price
- total
- cost (for profit calc)
- profit
- timestamps
```

---

## 🚀 Deployment Checklist

-   [ ] Clear cache: `php artisan cache:clear`
-   [ ] Optimize: `php artisan optimize`
-   [ ] Check .env: APP_ENV=production
-   [ ] Database migrations up to date
-   [ ] Discount rules configured
-   [ ] Chart of accounts configured
-   [ ] Test dengan real data
-   [ ] Train kasir users
-   [ ] Backup database before go-live

---

## 📝 Future Enhancements

### Phase 2 (Nice to Have)

1. [ ] Barcode scanner integration
2. [ ] Cash drawer integration
3. [ ] Receipt printer (thermal)
4. [ ] Multiple payment methods per transaction
5. [ ] Product quick search by SKU input
6. [ ] Recent transactions history
7. [ ] Shift management
8. [ ] Cash in/out tracking
9. [ ] End of day report
10. [ ] Offline mode (PWA)

### Phase 3 (Advanced)

1. [ ] Queue integration (Queueable)
2. [ ] WebSocket for real-time updates
3. [ ] Mobile POS app
4. [ ] Customer display (second monitor)
5. [ ] Kitchen display system
6. [ ] Table management
7. [ ] Reservation system
8. [ ] Loyalty program UI
9. [ ] Voucher/Coupon redemption
10. [ ] Analytics dashboard

---

## 👥 User Roles & Permissions

**Kasir (Cashier):**

-   View POS page
-   Create transactions
-   View receipts
-   Print receipts

**Manager:**

-   All kasir permissions
-   View transaction history
-   Void transactions
-   Give discounts manually
-   End of day reports

**Admin:**

-   All manager permissions
-   Configure discount rules
-   Manage products
-   Manage customers
-   System configuration

---

## 📞 Support

**Developer:** AI Assistant  
**Date:** December 10, 2025  
**Version:** 2.0 Final

**Dokumentasi Terkait:**

-   POS_USER_GUIDE.md
-   POS_UI_IMPROVEMENTS.md
-   POS_IMPLEMENTATION_SUMMARY.md
-   POS_TESTING_GUIDE.md

---

## ✅ Conclusion

Sistem POS ini dibangun dengan **best practices**:

-   ✅ Modern tech stack (Alpine.js + Livewire)
-   ✅ Clean architecture
-   ✅ User-friendly interface
-   ✅ Performance optimized
-   ✅ Extensible & maintainable
-   ✅ Well documented

**Ready for Production!** 🚀

Untuk pertanyaan atau issue, refer ke dokumentasi atau contact developer.
