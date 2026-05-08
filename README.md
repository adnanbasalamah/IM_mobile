# IkhwanMart Mobile

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chart.js&logoColor=white" alt="Chart.js">
</p>

Aplikasi web mobile-first untuk monitoring penjualan OSPOS. Dibangun dengan PHP vanilla tanpa framework, tanpa CDN — semua aset diload secara lokal.

## Fitur

| Halaman | Fungsi |
|---------|--------|
| **Dashboard** | Grafik penjualan per jam, jumlah & nilai transaksi, tabel breakdown per payment type |
| **Cek Transfer** | Daftar transaksi Transfer per tanggal, klik pelanggan → buka nota |
| **Lihat Nota** | Detail receipt penjualan + tombol kirim WhatsApp & copy |
| **Cek Stok** | Daftar barang stok rendah per kategori, dikelompokkan per supplier |
| **Cek Kasir** | Input kutipan tunai & uang di kasir, auto-calculate total |

## Struktur Proyek

```
im_mobile/
├── index.php              # Router utama + shell HTML
├── config.php              # Koneksi DB (PDO), session, helper
├── auth.php                # Login & logout
├── .htaccess               # Apache rewrite rules
├── api/
│   ├── dashboard.php       # Data transaksi per jam
│   ├── transfer.php        # Daftar transaksi transfer
│   ├── nota.php            # Detail nota penjualan
│   ├── stok.php            # Stok rendah per kategori
│   ├── categories.php       # List kategori dari OSPOS
│   ├── employees.php       # List kasir/employee
│   └── kasir_save.php      # Simpan data kasir
├── pages/
│   ├── login.php           # Halaman login
│   ├── dashboard.php        # Dashboard view
│   ├── transfer.php         # Transfer view
│   ├── nota.php             # Nota view
│   ├── stok.php             # Stok view
│   └── kasir.php             # Kasir view
└── assets/
    ├── css/style.css        # Design system (~29KB)
    ├── js/app.js            # SPA routing & interaksi
    ├── js/chart.umd.js      # Chart.js v4 lokal
    └── fonts/               # Inter & Manrope (woff2 subset)
```

## Setup

1. Clone repo ke document root web server
2. Pastikan database OSPOS sudah berjalan di `localhost`
3. Sesuaikan koneksi DB di `config.php`:

```php
DB_HOST: localhost
DB_NAME: ospos
DB_USER: ospos
DB_PASS: ospos
```

4. Akses `http://localhost/im_mobile/` di browser

## Autentikasi

- Login menggunakan tabel `ospos_employees` + `ospos_people`
- Password: `hash_version = 2` → bcrypt, `hash_version = 1` → sha1
- Logout via klik avatar (pojok kanan atas)

## Teknologi

- **Backend**: PHP 7.4+ vanilla (tanpa framework)
- **Database**: MySQL/MariaDB (database `ospos`)
- **Frontend**: HTML + CSS custom + JavaScript vanilla
- **Chart**: Chart.js v4 (file lokal)
- **Font**: Inter + Manrope (woff2 lokal, latin + latin-ext subset)
- **Icon**: Inline SVG
- **Server**: Apache dengan `.htaccess`

## Desain

Mobile-first design system dengan Material Design 3 inspiration:

- Warna utama `#1e40af` (biru)
- Card-based layout dengan shadow halus
- Bottom navigation bar (Dashboard, Transfer, Stock, Kasir)
- Touch-friendly: minimal 44px tap target, input 48px height
- Font display: Manrope 800, font body: Inter 400-600

## Database Schema

Menggunakan tabel OSPOS yang sudah ada:

| Tabel | Digunakan di |
|-------|-------------|
| `ospos_employees` | Login, Kasir |
| `ospos_people` | Login, Transfer, Nota, Stok |
| `ospos_sales` | Dashboard, Transfer, Nota |
| `ospos_sales_payments` | Dashboard, Transfer, Nota |
| `ospos_sales_items` | Nota |
| `ospos_items` | Nota, Stok |
| `ospos_item_quantities` | Stok |
| `ospos_suppliers` | Stok |
| `ospos_customers` | Transfer, Nota |
| `ospos_cash_records` | Kasir (INSERT) |

## Lisensi

Proyek internal — untuk penggunaan toko IkhwanMart.