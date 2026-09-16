# Rebuild BigSIRS

Rebuild BigSIRS adalah proyek pembangunan ulang sistem BigSIRS PT Sisfomedika dengan arsitektur Laravel sebagai REST API dan Vue.js sebagai frontend.

## Stack

- Laravel REST API
- Vue 3
- Vite
- MySQL 8.4

## Ruang Lingkup

### Rawat Jalan

- Dashboard
- Pendaftaran
- Admisi
- Pemeriksaan
- Billing

### Logistik

- Faktur
- Mutasi
- Stok Opname
- Pengeluaran Barang
- Penjualan Bebas

## Struktur Folder

- `backend/`: aplikasi Laravel REST API.
- `frontend/`: aplikasi Vue 3 dengan Vite.
- `docs/`: dokumentasi API, database, testing, dan catatan rapat.

## Prasyarat

- PHP sesuai kebutuhan Laravel
- Composer
- Node.js
- npm
- MySQL 8.4
- Laragon untuk lingkungan lokal Windows

## Menjalankan Backend

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
php artisan serve
```

## Menjalankan Frontend

```bash
cd frontend
npm install
npm run dev
```

## Aturan Keamanan Data

- Jangan commit file `.env`, kredensial, password, token, API key, private key, atau konfigurasi lokal sensitif.
- Jangan menyimpan data pasien asli di repository.
- Jangan commit database dump, file backup, atau hasil ekspor data pasien.
- Gunakan data dummy atau data anonim untuk testing dan dokumentasi.
