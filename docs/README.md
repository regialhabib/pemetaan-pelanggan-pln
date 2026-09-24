# Dokumentasi Sistem Pemetaan Pelanggan PLN (ULP Telanaipura)

Selamat datang di dokumentasi resmi proyek **Sistem Informasi Geografis (SIG / GIS) Pemetaan Pelanggan PLN**. Aplikasi ini dirancang untuk memudahkan manajemen pelanggan, pemetaan visual titik lokasi gardu/pelanggan, penugasan kunjungan ke petugas lapangan, serta optimasi rute perjalanan petugas menggunakan OSRM (*Traveling Salesperson Problem*).

---

## 📚 Struktur Dokumentasi

Dokumentasi ini disusun secara modular dalam beberapa dokumen terpisah:

1. [**Arsitektur & Modul Sistem**](./arsitektur-dan-sistem.md)  
   Penjelasan arsitektur aplikasi (Laravel 10 + Modular JS), integrasi Leaflet GIS, optimasi rute OSRM, dan sistem impor Excel asinkron.
2. [**Panduan Peran Pengguna & Alur Kerja**](./panduan-user-dan-role.md)  
   Matriks hak akses (Admin vs Petugas Lapangan), alur siklus tugas kunjungan, pembaruan status lapangan, serta laporan dan pencetakan PDF.
3. [**Skema Database & Relasi Model**](./skema-database.md)  
   Diagram Relasi Entitas (ERD), kamus data tabel, indeks performa, dan relasi Eloquent ORM.
4. [**Rencana & Catatan Tindak Lanjut Temuan**](./rencana-perbaikan-temuan.md)  
   Dokumentasi hasil audit kode (*code review*), perbaikan inkonsistensi enum, dan peningkatan fitur lanjutan.

---

## 🚀 Panduan Memulai Cepat (Quick Start)

### Persyaratan Sistem:
- PHP >= 8.1
- Composer >= 2.x
- Node.js >= 18.x & NPM
- MySQL Server >= 8.0 / MariaDB >= 10.4

### Instalasi & Menjalankan Proyek:

```bash
# 1. Clone repositori & masuk ke direktori
cd PLN

# 2. Instal dependensi PHP (Composer)
composer install

# 3. Instal dependensi Node.js & Vite
npm install

# 4. Konfigurasi Environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi Database pada berkas .env
# DB_DATABASE=PLN
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan Migrasi Database & Seeder
php artisan migrate --seed

# 7. Jalankan Server Pengembangan (Terminal 1)
php artisan serve

# 8. Jalankan Vite Asset Bundler (Terminal 2)
npm run dev
```

---

## 👥 Pengguna Bawaan (Default Seeder)

Setelah menjalankan seeder:
- **Role Admin**: Memiliki akses ke seluruh menu dashboard, data pelanggan, penugasan, dan laporan.
- **Role Petugas**: Memiliki akses ke peta penugasan rute lapangan dan daftar riwayat kunjungan.
