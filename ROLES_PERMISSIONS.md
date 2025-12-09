# Bakery ERP - User Roles & Permissions Guide

## 📋 Summary Setup

### Database & Migrations

-   ✅ 12 migrations executed successfully
-   ✅ SQLite database ready
-   ✅ 32 Chart of Accounts seeded
-   ✅ 8 Products seeded (5 raw materials, 3 finished goods)
-   ✅ 2 BOMs seeded (Roti Tawar, Croissant)

### Roles Created

-   ✅ **super_admin** - Full system access
-   ✅ **head_baker** - Recipe & production management
-   ✅ **inventory_controller** - Stock & inventory management
-   ✅ **cashier** - POS & sales transactions
-   ✅ **production_staff** - View recipes & record production

### Test Users Created

| Email                 | Password | Role                 | Name                               |
| --------------------- | -------- | -------------------- | ---------------------------------- |
| admin@bakery.com      | password | super_admin          | Super Admin                        |
| baker@bakery.com      | password | head_baker           | Budi Santoso (Head Baker)          |
| inventory@bakery.com  | password | inventory_controller | Siti Rahayu (Inventory Controller) |
| cashier@bakery.com    | password | cashier              | Ani Wijaya (Cashier)               |
| production@bakery.com | password | production_staff     | Joko Susilo (Production Staff)     |

---

## 🔐 Recommended Permissions per Role

### 1. **Head Baker** (`head_baker`)

**Purpose:** Manages recipes, production planning, and product specifications

**Recommended Permissions:**

-   **Products:**
    -   ✅ view_any_product
    -   ✅ view_product
    -   ✅ create_product
    -   ✅ update_product
    -   ❌ delete_product (admin only)
-   **BOM Headers (Resep):**
    -   ✅ view_any_bom_header
    -   ✅ view_bom_header
    -   ✅ create_bom_header
    -   ✅ update_bom_header
    -   ✅ delete_bom_header
-   **Chart of Accounts:**
    -   ✅ view_any_chart_of_account (view only, untuk cek cost)
    -   ✅ view_chart_of_account

### 2. **Inventory Controller** (`inventory_controller`)

**Purpose:** Manages inventory, stock levels, purchase orders

**Recommended Permissions:**

-   **Products:**
    -   ✅ view_any_product
    -   ✅ view_product
    -   ✅ update_product (untuk update stock info)
    -   ❌ create_product
    -   ❌ delete_product
-   **Chart of Accounts:**

    -   ✅ view_any_chart_of_account
    -   ✅ view_chart_of_account

-   **BOM Headers:**
    -   ✅ view_any_bom_header (untuk tahu kebutuhan bahan)
    -   ✅ view_bom_header
    -   ❌ create/update/delete (read only)

### 3. **Cashier** (`cashier`)

**Purpose:** POS operations, sales transactions

**Recommended Permissions:**

-   **Products:**

    -   ✅ view_any_product (filter: is_sellable only)
    -   ✅ view_product
    -   ❌ create/update/delete

-   **Chart of Accounts:**

    -   ❌ No access (tidak perlu)

-   **BOM Headers:**
    -   ❌ No access (tidak perlu)

### 4. **Production Staff** (`production_staff`)

**Purpose:** View recipes and record production activities

**Recommended Permissions:**

-   **Products:**

    -   ✅ view_any_product
    -   ✅ view_product
    -   ❌ create/update/delete

-   **BOM Headers:**

    -   ✅ view_any_bom_header
    -   ✅ view_bom_header
    -   ❌ create/update/delete (read only)

-   **Chart of Accounts:**
    -   ❌ No access

---

## 🎯 Next Steps - Manual Permission Assignment

### 1. Login sebagai Admin

```
URL: http://localhost/admin/login
Email: admin@bakery.com
Password: password
```

### 2. Akses Filament Shield

-   Buka sidebar: **Filament Shield** → **Roles**
-   Atau langsung ke: `http://localhost/admin/shield/roles`

### 3. Edit Each Role

Klik setiap role dan centang permissions sesuai tabel di atas:

#### Head Baker

1. Klik **head_baker** role
2. Centang permissions:
    - Product: view_any, view, create, update
    - BOM Header: view_any, view, create, update, delete
    - Chart of Account: view_any, view
3. Click **Save**

#### Inventory Controller

1. Klik **inventory_controller** role
2. Centang permissions:
    - Product: view_any, view, update
    - BOM Header: view_any, view
    - Chart of Account: view_any, view
3. Click **Save**

#### Cashier

1. Klik **cashier** role
2. Centang permissions:
    - Product: view_any, view
3. Click **Save**

#### Production Staff

1. Klik **production_staff** role
2. Centang permissions:
    - Product: view_any, view
    - BOM Header: view_any, view
3. Click **Save**

### 4. Test Each Role

Logout dan login dengan setiap test user untuk verify permissions:

-   baker@bakery.com - harus bisa CRUD products & BOMs
-   inventory@bakery.com - harus bisa view products & BOMs, update products
-   cashier@bakery.com - hanya bisa view products
-   production@bakery.com - hanya bisa view products & BOMs

---

## 📊 Navigation Groups (Already Configured)

### Manajemen User

-   Users

### Akuntansi

-   Bagan Akun (Chart of Accounts)

### Produksi

-   Produk (Products)
-   Resep Produksi (BOM Headers)

---

## 🛠️ Technical Details

### Form Max Width

-   Products: `7xl` (1280px)
-   BOM Headers: `full` (hampir full screen untuk comfortable editing)

### Product Filters (ListProducts)

-   Type (raw, wip, finished, consumable, service)
-   Is Sellable (Yes/No)
-   Is Purchasable (Yes/No)
-   Is Active (Yes/No)

### BOM Features

-   ✅ Repeater for ingredients
-   ✅ Reorderable with buttons (↑↓)
-   ✅ Cloneable items (📋 icon)
-   ✅ Collapsible items
-   ✅ Item labels show: 📦 Product name (quantity UoM) • Waste: X%
-   ✅ Delete confirmation modal
-   ✅ Helper texts in Indonesian
-   ✅ Tabs: Recipe Information | Ingredients (Bahan Baku)

---

## 🔄 Commands Reference

### Reset Database with All Seeders

```bash
php artisan migrate:fresh --seed
```

### Generate Permissions for All Resources

```bash
php artisan shield:generate --all
```

### Create Super Admin User

```bash
php artisan db:seed --class=SuperAdminSeeder
```

### Assign Super Admin Role (if needed)

```bash
php artisan shield:super-admin
```

---

## ✅ Checklist - All Completed

-   [x] BOM error fixed (bom_header_id consistency)
-   [x] Navigation grouping (3 groups in Indonesian)
-   [x] BOM renamed to "Resep Produksi"
-   [x] Product table enhanced with filters and badges
-   [x] Form max width optimized (Products: 7xl, BOM: full)
-   [x] 4 roles created with test users
-   [x] 55 permissions generated
-   [x] Super admin user ready
-   [ ] **PENDING:** Manual permission assignment via admin UI

---

## 📝 Notes

-   Permissions tidak di-assign otomatis via seeder
-   Admin harus assign permissions manual lewat UI Filament Shield
-   Lebih flexible dan mudah untuk adjust permissions nanti
-   Test users semua menggunakan password: `password`
-   Database: SQLite (database/database.sqlite)

---

**Last Updated:** December 8, 2025
**System:** BakerySys v2 - Laravel 12 + Filament v4
