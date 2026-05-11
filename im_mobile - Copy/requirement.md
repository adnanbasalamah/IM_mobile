# IM Mobile App - Requirement Document

## 1. Overview

Aplikasi web PHP vanilla yang terhubung ke database OSPOS (Open Source Point of Sale) untuk menampilkan laporan dan pencatatan kasir. Dibuka di browser HP (mobile-first). Tidak menggunakan framework, tidak menggunakan CDN - semua aset lokal.

## 2. Tech Stack

- **Backend**: PHP 7.4+ vanilla (tanpa framework)
- **Database**: MySQL/MariaDB (database `ospos` yang sudah ada)
- **Frontend**: HTML + CSS custom + JavaScript vanilla
- **Chart**: Chart.js v4 (file lokal `chart.umd.js`)
- **Font**: Inter + Manrope (woff2 lokal, latin + latin-ext subset)
- **Icon**: Inline SVG (Material Symbols style)
- **Server**: Apache dengan `.htaccess`

## 3. Struktur File

```
im_mobile/
├── index.php              # Router utama + shell HTML (header, bottom nav)
├── config.php              # DB connection (PDO), session, helper functions
├── auth.php                # Login (POST) & logout (GET ?action=logout)
├── .htaccess               # Apache rewrite
├── api/
│   ├── dashboard.php       # GET ?date=YYYY-MM-DD → JSON data transaksi per jam
│   ├── transfer.php         # GET ?date=YYYY-MM-DD → JSON daftar transfer
│   ├── nota.php             # GET ?id=<sale_id> → JSON detail nota penjualan
│   ├── stok.php             # GET ?kategori=<nama> → JSON stok rendah per kategori
│   ├── categories.php       # GET → JSON list kategori dari ospos_items.category
│   ├── employees.php        # GET → JSON list kasir/employee
│   └── kasir_save.php       # POST JSON → INSERT ke ospos_cash_records
├── pages/
│   ├── login.php            # Form username + password
│   ├── dashboard.php        # Date picker + metrics + bar chart + payment table
│   ├── transfer.php         # Date picker + tabel transaksi transfer
│   ├── nota.php             # Receipt/nota + tombol WhatsApp + Copy
│   ├── stok.php             # Dropdown kategori + daftar stok rendah
│   └── kasir.php            # Form input uang kasir
└── assets/
    ├── css/style.css        # Design system CSS (~29KB)
    ├── js/app.js            # SPA routing + interaksi (~26KB)
    ├── js/chart.umd.js      # Chart.js v4 lokal (~206KB)
    └── fonts/
        ├── Inter-Latin.woff2
        ├── Inter-LatinExt.woff2
        ├── Manrope-Latin.woff2
        └── Manrope-LatinExt.woff2
```

## 4. Database Configuration

```php
DB_HOST: localhost
DB_NAME: ospos
DB_USER: ospos
DB_PASS: ospos
```

Konfigurasi ada di `config.php`. Session menggunakan PHP native session.

## 5. Autentikasi

### Login
- Tabel: `ospos_employees` JOIN `ospos_people` (ON `person_id`)
- Query: `SELECT e.person_id, e.username, e.password, e.hash_version, p.first_name, p.last_name FROM ospos_employees e JOIN ospos_people p ON e.person_id = p.person_id WHERE e.username = ? AND e.deleted = 0`
- Password verification:
  - `hash_version = 2` → `password_verify($input, $stored)`
  - `hash_version = 1` (atau lainnya) → `$stored == sha1($input)`
- Setelah berhasil: set `$_SESSION['user_id']`, `$_SESSION['username']`, `$_SESSION['first_name']`, `$_SESSION['last_name']`
- Redirect ke `index.php?page=dashboard`

### Logout
- Klik avatar pengguna (pojok kanan atas header) → link ke `auth.php?action=logout`
- `auth.php` destroy session, redirect ke login

### Session Guard
- Setiap halaman (kecuali login) dicek oleh `requireLogin()` di `config.php`
- Jika belum login, redirect ke `index.php?page=login`
- API endpoint juga cek session, return 401 jika Unauthorized

## 6. Halaman dan Fitur

---

### 6.1 Login (`pages/login.php`)

- Full-page (tanpa header/navbar)
- Form: input Username + Password + tombol Login
- Error message ditampilkan jika gagal (menggunakan `$_SESSION['login_error']`)
- Password toggle (show/hide)
- Desain mengikuti `stitch_login/code.html`

---

### 6.2 Dashboard (`pages/dashboard.php`)

#### Data yang ditampilkan:
1. **Jumlah Transaksi** — total `sale_id` unik di tanggal terpilih
2. **Nilai Transaksi** — total bersih (payment_amount - cash_refund untuk Tunai, payment_amount untuk non-Tunai)
3. **Bar Chart** — sumbu X = jam (06:00 s.d 21:00), sumbu Y = nilai penjualan bersih
4. **Tabel Payment Breakdown** — per jam: Tunai, Debit, Transfer, QRIS

#### API: `api/dashboard.php?date=YYYY-MM-DD`

**Query utama:**
```sql
SELECT HOUR(s.sale_time) as jam,
       sp.payment_type,
       SUM(sp.payment_amount) as total_payment,
       SUM(sp.cash_refund) as total_refund
FROM ospos_sales s
JOIN ospos_sales_payments sp ON s.sale_id = sp.sale_id
WHERE DATE(s.sale_time) = ?
  AND s.sale_status = 0
GROUP BY HOUR(s.sale_time), sp.payment_type
ORDER BY jam
```

**Logika perhitungan:**
- Untuk payment_type yang mengandung "cash" atau "tunai": `net = total_payment - total_refund`
- Untuk payment_type lainnya (debit, transfer, qris): `net = total_payment`
- Jika net < 0, set ke 0
- `payment_type` matching:
  - Tunai: mengandung "cash" atau "tunai"
  - Debit: mengandung "debit"
  - Transfer: mengandung "transfer"
  - QRIS: mengandung "qris"
  - Lainnya: dikategorikan sebagai Tunai

**Query jumlah transaksi:**
```sql
SELECT COUNT(DISTINCT sale_id) FROM ospos_sales WHERE DATE(sale_time) = ? AND sale_status = 0
```

**Response JSON:**
```json
{
  "date": "2024-01-15",
  "total_jumlah": 142,
  "total_nilai": 12450000,
  "hourly": [
    { "jam": 6, "jumlah_transaksi": 5, "nilai_transaksi": 250000, "tunai": 205000, "debit": 0, "transfer": 0, "qris": 45000 },
    ...
  ]
}
```

---

### 6.3 Cek Transfer (`pages/transfer.php`)

#### Data yang ditampilkan:
Tabel daftar transaksi dengan pembayaran Transfer di tanggal tertentu:
- Jam transaksi (format HH:MM dari sale_time, bukan hanya HH:00)
- Nama kasir
- Nama pelanggan (klikable → buka nota)
- Nilai transaksi

#### API: `api/transfer.php?date=YYYY-MM-DD`

**Query:**
```sql
SELECT s.sale_id,
       HOUR(s.sale_time) as jam,
       s.sale_time,
       CONCAT(emp.first_name, ' ', emp.last_name) as kasir_nama,
       s.customer_id,
       sp.payment_amount,
       sp.payment_type,
       COALESCE(c.company_name, CONCAT(cust.first_name, ' ', cust.last_name)) as pelanggan_nama
FROM ospos_sales s
JOIN ospos_sales_payments sp ON s.sale_id = sp.sale_id
JOIN ospos_employees e ON s.employee_id = e.person_id
JOIN ospos_people emp ON e.person_id = emp.person_id
LEFT JOIN ospos_customers c ON s.customer_id = c.person_id
LEFT JOIN ospos_people cust ON c.person_id = cust.person_id
WHERE DATE(s.sale_time) = ?
  AND sp.payment_type LIKE '%Transfer%'
  AND s.sale_status = 0
  AND sp.cash_adjustment = 0
ORDER BY s.sale_time ASC
```

**Catatan penting:**
- `jam_fmt` diambil dari substring `sale_time` (format `YYYY-MM-DD HH:MM:SS`, ambil `HH:MM`), BUKAN dari `HOUR(sale_time)` + `:00`
- Jika `customer_id` kosong atau `pelanggan_nama` kosong, tampilkan "Umum"
- Deduplikasi `sale_id` (satu sale bisa punya multiple payment rows, ambil yang pertama saja)
- `sp.cash_adjustment = 0` untuk mengecualikan adjustment entries

**Klik pelanggan** → navigasi ke halaman Nota dengan `sale_id` terkait

---

### 6.4 Lihat Nota (`pages/nota.php`)

#### Data yang ditampilkan:
- Header: "IkhwanMart", No. Nota (format `#TRX-<sale_id>`), pelanggan, waktu, tipe pembayaran
- Daftar item: nama, qty × harga satuan = subtotal per item
- Subtotal & Total Akhir
- Tombol "Kirim ke WhatsApp" (hijau #25D366) → `https://wa.me/?text=<encoded_text>`
- Tombol "Copy Nota" (biru #1e40af) → copy teks nota ke clipboard

#### API: `api/nota.php?id=<sale_id>`

**Query utama:**
```sql
-- Sale info
SELECT s.sale_id, s.sale_time, s.customer_id,
       CONCAT(emp.first_name, ' ', emp.last_name) as kasir_nama
FROM ospos_sales s
JOIN ospos_employees e ON s.employee_id = e.person_id
JOIN ospos_people emp ON e.person_id = emp.person_id
WHERE s.sale_id = ?

-- Sale items
SELECT si.item_id, si.quantity_purchased, si.item_unit_price,
       si.discount, si.discount_type,
       i.name as item_name, i.item_number
FROM ospos_sales_items si
JOIN ospos_items i ON si.item_id = i.item_id
WHERE si.sale_id = ?

-- Payments
SELECT payment_type, payment_amount FROM ospos_sales_payments WHERE sale_id = ?

-- Customer name (if customer_id exists)
SELECT COALESCE(c.company_name, CONCAT(p.first_name, ' ', p.last_name)) as nama
FROM ospos_customers c
JOIN ospos_people p ON c.person_id = p.person_id
WHERE c.person_id = ?
```

**Perhitungan line_total per item:**
- `discount_type = 1` (fixed): `line_total = (unit_price * qty) - (discount * qty)`
- `discount_type = 0` (percent) dengan discount > 0: `line_total = (unit_price * qty) * (1 - discount/100)`
- Lainnya: `line_total = unit_price * qty - discount`

**Navigasi ke nota:** via `IM.openNota(saleId)` dari halaman transfer, atau langsung load via `window._pendingNotaId`

---

### 6.5 Cek Stok (`pages/stok.php`)

#### Dropdown Kategori
- Default: "Belum Dipilih" → list kosong, tampilkan pesan "Pilih kategori terlebih dahulu"
- Hanya tampilkan data setelah user memilih kategori
- Kategori diambil dari kolom `ospos_items.category` (bukan dari attribute tables)

#### API: `api/categories.php`

```sql
SELECT DISTINCT category FROM ospos_items WHERE deleted = 0 AND category IS NOT NULL AND category != '' ORDER BY category
```

Response: `{ "categories": ["SEMBAKO", "MINUMAN", ...] }`

#### API: `api/stok.php?kategori=<nama_kategori>`

Jika `kategori` kosong → return `{ "items": [] }`

```sql
SELECT i.name, i.item_number, i.category, i.pack_name, i.reorder_level,
       iq.quantity,
       COALESCE(s.company_name, 'Tanpa Supplier') AS supplier_nama
FROM ospos_items i
JOIN ospos_item_quantities iq ON i.item_id = iq.item_id AND iq.location_id = 1
LEFT JOIN ospos_suppliers s ON i.supplier_id = s.person_id
WHERE i.deleted = 0
  AND iq.quantity <= i.reorder_level
  AND i.category = ?
ORDER BY supplier_nama, i.name
```

**Catatan penting:**
- Stok rendah = `quantity <= reorder_level`
- Hanya `location_id = 1` (toko utama)
- Di-group by supplier di PHP layer
- `pack_name` ditampilkan sebagai unit (misal "PCS", "CTN", "PACK")

**Response JSON:**
```json
{
  "items": [
    {
      "supplier": "PT. Indofood",
      "items": [
        { "name": "Indomie Goreng", "sku": "IND-001", "pack_name": "PCS", "quantity": 12, "reorder_level": 24 },
        ...
      ]
    },
    ...
  ]
}
```

**Tombol WhatsApp & Copy:** di bagian bawah list, format teks:
```
*Stok Rendah IkhwanMart*

_PT. Indofood_
• Indomie Goreng: 12 PCS (min: 24)
...
```

---

### 6.6 Cek Kasir (`pages/kasir.php`)

#### Bagian 1: Info Header
- Date picker + Time picker (auto-fill waktu sekarang)
- Dropdown Kasir (dari `ospos_employees` JOIN `ospos_people`)

#### API: `api/employees.php`
```sql
SELECT e.person_id, CONCAT(p.first_name, ' ', p.last_name) as nama
FROM ospos_employees e
JOIN ospos_people p ON e.person_id = p.person_id WHERE e.deleted = 0 ORDER BY p.first_name
```

#### Bagian 2: Kutipan Tunai (BESAR)
- Input jumlah lembar: Rp 100.000, Rp 50.000
- Auto-calculate: total_kutipan = (rp100k × 100000) + (rp50k × 50000)

#### Bagian 3: Uang di Kasir (KECIL/KOIN)
- Input jumlah lembar: Rp 20.000, Rp 10.000, Rp 5.000, Rp 2.000, Rp 1.000
- Input total koin (dalam Rupiah, bukan jumlah koin)
- Auto-calculate: total_di_kasir = total_kutipan + (rp20k × 20000) + (rp10k × 10000) + (rp5k × 5000) + (rp2k × 2000) + (rp1k × 1000) + coin_total

#### Submit: `api/kasir_save.php` (POST JSON)

**Tabel target:** `ospos_cash_records`

```sql
INSERT INTO ospos_cash_records
(person_id, cashier, record_date, record_time, rp100k, rp50k, rp20k, rp10k, rp5k, rp2k, rp1k, coin_total, total_kutipan, total_di_kasir)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
```

**Kolom `cashier`** diisi dari nama employee yang dipilih di dropdown (text, bukan ID).

---

## 7. Navigasi

### Header (pojok kanan atas)
- Logo IkhwanMart + ikon toko (SVG)
- Avatar pengguna (inisial) → klik untuk logout (link ke `auth.php?action=logout`)

### Bottom Navbar
- 4 tab: Dashboard | Transfer | Stock | Kasir
- Tab aktif: background `var(--secondary-container)`, text `var(--on-secondary-container)`, border-radius pill
- Tab tidak aktif: text `var(--secondary)`, opacity 0.7
- Fixed di bawah layar, z-index 50

### SPA-like Navigation
- Hash-based routing (`#dashboard`, `#transfer`, `#stok`, `#kasir`)
- `IM.navigateTo(page)` fetch halaman via AJAX, replace `#content` innerHTML
- Setelah load, panggil `IM.afterPageLoad(page)` untuk inisialisasi JS (chart, date picker, dll)

---

## 8. Design System

### Warna (CSS Variables)
```css
--primary: #1e40af;
--primary-dark: #00288e;
--primary-container: #1e40af;
--on-primary: #ffffff;
--primary-fixed: #dde1ff;
--secondary: #555f70;
--on-secondary: #ffffff;
--secondary-container: #d6e0f4;
--on-secondary-container: #596374;
--surface: #f8f9fb;
--surface-container-low: #f3f4f6;
--surface-container-lowest: #ffffff;
--surface-container: #edeef0;
--surface-container-high: #e7e8ea;
--surface-container-highest: #e1e2e4;
--on-surface: #191c1e;
--on-surface-variant: #444653;
--outline: #757684;
--outline-variant: #c4c5d5;
--error: #ba1a1a;
--whatsapp: #25D366;
```

### Typography
- **Display/Headline**: Manrope (bold 800, 1.5rem+)
- **Body/Label**: Inter (400-600, 0.75rem-1rem)
- Font loaded from lokal woff2 files with `unicode-range` subsets

### Spacing
- Container padding: 16px
- Element gap: 12px
- Input height: 48px
- Touch target min: 44px
- Border radius: 0.5rem (8px)

### Component Patterns
- **Cards**: `background: var(--surface-container-lowest)`, `border-radius: 0.75rem`, `box-shadow: 0 2px 4px rgba(0,0,0,0.05)`, tanpa border solid
- **Buttons Primary**: `background: var(--primary-container)`, `color: var(--on-primary)`, full-width
- **Input fields**: `border: 2px solid var(--outline-variant)`, `border-radius: 0.5rem`, `height: 48px`, focus state `border-color: var(--primary)`
- **Section headers**: Label uppercase kecil + judul besar Manrope

---

## 9. Database Schema (Tabel yang Digunakan)

| Tabel | Digunakan di | Kolom Penting |
|-------|-------------|---------------|
| `ospos_employees` | Login, Kasir | `person_id`, `username`, `password`, `hash_version`, `deleted` |
| `ospos_people` | Login, Transfer, Nota, Stok | `person_id`, `first_name`, `last_name`, `company_name` |
| `ospos_sales` | Dashboard, Transfer, Nota | `sale_id`, `sale_time`, `customer_id`, `employee_id`, `sale_status` |
| `ospos_sales_payments` | Dashboard, Transfer, Nota | `payment_id`, `sale_id`, `payment_type`, `payment_amount`, `cash_refund`, `cash_adjustment` |
| `ospos_sales_items` | Nota | `sale_id`, `item_id`, `quantity_purchased`, `item_unit_price`, `discount`, `discount_type` |
| `ospos_items` | Nota, Stok | `item_id`, `name`, `item_number`, `category`, `pack_name`, `supplier_id`, `reorder_level`, `deleted` |
| `ospos_item_quantities` | Stok | `item_id`, `location_id`, `quantity` |
| `ospos_suppliers` | Stok | `person_id`, `company_name` |
| `ospos_customers` | Transfer, Nota | `person_id`, `company_name` |
| `ospos_cash_records` | Kasir (INSERT) | `id`, `person_id`, `cashier`, `record_date`, `record_time`, `rp100k`-`rp1k`, `coin_total`, `total_kutipan`, `total_di_kasir` |

### Catatan Penting tentang Data

1. **`sale_status = 0`** berarti transaksi completed (bukan void/canceled)
2. **`payment_type`** di `ospos_sales_payments` berupa string seperti "Cash", "Debit", "Transfer", "QRIS" — matching dilakukan dengan `LIKE '%Transfer%'` dll
3. **`cash_refund`** di `ospos_sales_payments` hanya relevan untuk pembayaran Cash, berisi jumlah kembalian yang harus dikurangi dari `payment_amount`
4. **`cash_adjustment = 0`** di query Transfer untuk mengecualikan adjustment entries
5. **`hash_version`** di `ospos_employees`: 2 = bcrypt (password_hash), 1 atau lainnya = sha1
6. **`quantity <= reorder_level`** mendefinisikan stok rendah, dengan `location_id = 1` (toko utama)
7. **`category`** di `ospos_items` adalah string biasa (bukan foreign key), berisi nilai seperti "SEMBAKO", "MINUMAN", dll — INI yang dipakai untuk dropdown kategori di Cek Stok, BUKAN `ospos_attribute_values`
8. **`ospos_items.pack_name`** berisi unit seperti "Each", "Pack", "Carton" dll

---

## 10. JavaScript Architecture (`app.js`)

Objek global `IM` dengan methods:

| Method | Fungsi |
|--------|--------|
| `init()` | Inisialisasi: baca hash, load halaman, bind navbar |
| `navigateTo(page)` | AJAX fetch `pages/<page>.php`, replace `#content` innerHTML |
| `initDashboard()` | Set date picker, load dashboard data |
| `loadDashboard(date)` | Fetch `api/dashboard.php?date=` |
| `renderDashboard(data)` | Render metrics, chart, table |
| `renderChart(hourly)` | Chart.js bar chart |
| `renderPaymentTable(hourly)` | Tabel breakdown per jam |
| `initTransfer()` | Set date picker, load transfer data |
| `loadTransfer(date)` | Fetch `api/transfer.php?date=` |
| `renderTransfer(data)` | Render list transaksi transfer |
| `openNota(saleId)` | Navigasi ke halaman nota dengan sale_id |
| `initNota()` | Load nota dari `window._pendingNotaId` |
| `loadNota(saleId)` | Fetch `api/nota.php?id=` |
| `renderNota(data)` | Render receipt card |
| `sendWhatsApp()` | Buka wa.me dengan teks nota |
| `copyNota()` | Copy teks nota ke clipboard |
| `initStok()` | Load categories, bind dropdown change |
| `loadCategories(select)` | Fetch `api/categories.php` |
| `loadStok(kategori)` | Fetch `api/stok.php?kategori=` (kosong = list kosong) |
| `renderStok(data)` | Render cards grouped by supplier |
| `sendStokWhatsApp()` | Buka wa.me dengan teks stok |
| `copyStokList()` | Copy teks stok ke clipboard |
| `initKasir()` | Set date/time, load employees, bind inputs |
| `calculateKasir()` | Auto-calculate total kutipan & total di kasir |
| `submitKasir()` | POST JSON ke `api/kasir_save.php` |

---

## 11. Perhitungan Logika Bisnis

### Dashboard - Nilai Transaksi per Payment Type
```
Jika payment_type seperti "Cash" atau "Tunai":
    nilai_bersih = SUM(payment_amount) - SUM(cash_refund)
Jika payment_type lain (Debit, Transfer, QRIS):
    nilai_bersih = SUM(payment_amount)
Jika nilai_bersih < 0, set ke 0
```

### Cek Transfer - Jam Transaksi
```
jam_fmt = substring(sale_time, 11, 5)  // Ambil HH:MM dari timestamp
// BUKAN HOUR(sale_time) + ":00"
```

### Cek Stok - Kategori
```
Kategori diambil dari kolom ospos_items.category (DISTINCT)
// BUKAN dari ospos_attribute_values
```

### Cek Stok - Stok Rendah
```
Kondisi: quantity <= reorder_level (bukan <)
Filter: location_id = 1
Group by: supplier_nama (di PHP layer)
```

### Kasir - Perhitungan
```
total_kutipan = (rp100k × 100000) + (rp50k × 50000)
total_di_kasir = total_kutipan + (rp20k × 20000) + (rp10k × 10000) + (rp5k × 5000) + (rp2k × 2000) + (rp1k × 1000) + coin_total
```