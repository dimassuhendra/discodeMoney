# 💼 Personal Finance Dashboard Application

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![SQLite](https://img.shields.io/badge/SQLite-Local_Dev-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://sqlite.org)

Sistem Aplikasi Dashboard Keuangan Pribadi (Personal Finance Dashboard) berbasis web modular yang dibangun menggunakan **Laravel 13**, **Alpine.js**, **Chart.js**, dan **Tailwind CSS**. Aplikasi ini dirancang untuk mencatat, mengelola, dan menganalisis arus kas (*cash flow*), portofolio investasi multi-aset, inventaris aset/barang, serta manajemen dompet/rekening secara otomatis dan terintegrasi.

---

## 🌟 Fitur Utama

### 📊 1. Dashboard Utama
- **Real-Time Net Worth Overview**: Rekap total saldo kas aktif, estimasi nilai portofolio investasi, dan total aset inventaris.
- **Visualisasi Visual**: Multi-variable charts (Doughnut Chart & Stacked Bar Chart) untuk memantau tren arus kas bulanan.
- **Budget Bar Benchmark**: Indikator kesehatan pengeluaran harian uang makan.

### 👛 2. Manajemen Sumber Dana (Centralized Wallet)
- **Multi-Account/Wallet Support**: Pencatatan kas terpisah (misal: *Bank Utama, E-Wallet, Kas Kecil, Uang Makan*).
- **Automated Balance Sync**: Saldo dompet/rekening bertambah otomatis saat Pemasukan dicatat dan berkurang saat Pengeluaran dicatat.
- **Aturan Budgeting Harian**: Fitur penetapan pagu harian khusus untuk pos tertentu (misal: *Uang Makan Rp40.000/hari*).

### 💸 3. Transaksi Pengeluaran Harian & Pemasukan
- **Pengeluaran Harian**:
  - Pencatatan pengeluaran terstruktur berdasarkan kategori dan tanggal.
  - **Highlight Analytics Uang Makan**: Insight khusus rata-rata pengeluaran harian, tanggal pengeluaran terbesar, dan terkecil untuk evaluasi kebiasaan konsumsi.
  - **Auto-Sync Draft Investasi & Inventaris**: Opsi pembuatan otomatis record ke *Portofolio Investasi* atau *Inventaris Barang* dari pengeluaran.
- **Pemasukan (Income Tracking)**:
  - Pencatatan kas masuk terikat ke rekening target.
  - Visualisasi tren pemasukan bulanan dan pencatatan berbasis periode/filter.

### 📈 4. Portofolio Investasi Multi-Aset
- **Support Multi-Aset**: Mendukung pencatatan Saham, Forex (USD, JPY), Reksadana, Crypto, dan Emas.
- **Tab Pemisah (Holding vs Sold/Realized)**:
  - **Portofolio Aktif (*Holding*)**: Aset yang masih dimiliki lengkap dengan kalkulasi durasi simpan (*holding days*).
  - **Histori Selesai (*Realized/Sold*)**: Catatan penjualan aset lengkap dengan perhitungan *Realized Gain/Loss* (IDR) dan % ROI.
- **Liquidation Flow**: Modal likuidasi interaktif untuk menutup posisi aset.
- **Detail Histori per Emiten/Aset**: Klik nama aset untuk melihat seluruh histori transaksi beli/jual historis beserta agregasi performa emiten tersebut.

### 📦 5. Inventaris Barang & Aset
- **Kategori Barang**:
  - *Barang Mati*: Item konsumtif & operasional (Laptop, Elektronik).
  - *Barang Hidup / Aset*: Item produktif (Hewan Ternak, Tanaman, Aset Fisik).
  - *Kebutuhan Rutin*: Sembako, toiletries, dan perlengkapan harian.
- **Chart Analysis**: Visualisasi porsi persentase jenis barang dan tren pembelian bulanan (Grouped Bar Chart dengan `=sum`).
- **Histori Agregasi Item**: Pencatatan log pembelian terperinci (frekuensi beli, total pengeluaran, rata-rata bulanan).

### ⚙️ 6. Pengaturan & Profil
- **Kelola Profil & Keamanan**: Pembaruan email dan perubahan kode akses/PIN terenkripsi (*Hashed*).
- **Export Data (Backup CSV)**: Fitur unduh seluruh riwayat transaksi untuk cadangan data mandiri.
- **Reset Data Transaksi (Danger Zone)**: Fitur reset seluruh data transaksi secara aman dengan konfirmasi PIN/kode akses.

---

## 🛠️ Teknologi & Arsitektur

- **Backend**: PHP 8.3 / Laravel 13.x
- **Frontend Reactivity**: Alpine.js 3.x
- **Styling UI**: Tailwind CSS & FontAwesome Icons
- **Data Visualization**: Chart.js & Chart.js Datalabels Plugin
- **Database**: SQLite (Local Development) / MySQL compatible
- **Architecture Pattern**:
  - **Form Request Layer** untuk validasi request.
  - **Service Layer Pattern** untuk memisahkan logika bisnis kompleks dari Controller.
  - **Blade Components** untuk modularitas UI.

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek di lingkungan pengembangan lokal Anda:

### 1. Prerequisites
Pastikan perangkat Anda telah terinstal:
- PHP >= 8.2
- Composer
- Node.js & NPM

### 2. Clone Repositori
```bash
git clone https://github.com/username/personal-finance-dashboard.git
cd personal-finance-dashboard
```

### 3. Instalasi Dependensi
```bash
# Instal dependensi PHP
composer install

# Instal dependensi JavaScript/CSS
npm install
```

### 4. Konfigurasi Environment
Salin berkas `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```

Buka `.env` dan atur database menggunakan SQLite:
```env
DB_CONNECTION=sqlite
# DB_DATABASE=/path/to/database.sqlite (biarkan default untuk Laravel 13)
```

Buat file database SQLite kosong (jika belum ada):
```bash
# Untuk Linux/macOS
touch database/database.sqlite

# Untuk Windows (PowerShell)
New-Item -ItemType File -Path database/database.sqlite
```

Generate Application Key:
```bash
php artisan key:generate
```

### 5. Migrasi Database
Jalankan migrasi tabel database:
```bash
php artisan migrate
```

### 6. Jalankan Server Lokal
Jalankan server Laravel dan build asset Tailwind/Alpine:
```bash
# Jalankan server Laravel
php artisan serve

# Di terminal terpisah, jalankan Vite/NPM dev
npm run dev
```

Buka browser dan akses aplikasi melalui `http://localhost:8000`.

---

## 📁 Struktur Folder Proyek Utama

```text
app/
├── Http/
│   ├── Controllers/          # Controller untuk setiap modul
│   └── Requests/             # Form Request Validation untuk semua modul
├── Models/                   # Model Eloquent (User, Pemasukan, Pengeluaran, Investasi, Barang, SumberDana)
└── Services/                 # Business Logic Layer (PemasukanService, PengeluaranService, InvestasiService, dll)
resources/
└── views/                    # Tampilan Blade UI (Sesuai dengan nama modul)
    ├── barang/
    ├── investasi/
    ├── pemasukan/
    ├── pengeluaran/
    ├── pengaturan/
    └── sumber_dana/
routes/
└── web.php                   # Routing Aplikasi
```

---

## 📄 Lisensi

Aplikasi ini dirilis di bawah lisensi [MIT License](LICENSE).