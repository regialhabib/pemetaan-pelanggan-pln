# Arsitektur & Modul Sistem

Dokumen ini menjelaskan rancangan arsitektur perangkat lunak, teknologi yang digunakan, modul peta GIS, serta mekanisme optimasi rute pada aplikasi **Pemetaan Pelanggan PLN (ULP Telanaipura)**.

---

## 1. Arsitektur Umum Sistem

Aplikasi ini menggunakan pola arsitektur **Model-View-Controller (MVC)** dari framework Laravel 10 pada sisi backend, dipadukan dengan struktur JavaScript modular pada sisi frontend untuk menangani logika peta GIS yang kompleks.

```
PLN/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Logika request-response (Dashboard, Pelanggan, TugasKunjungan)
│   │   └── Middleware/         # AdminMiddleware (Proteksi hak akses)
│   ├── Imports/                # PelangganImport (Chunk & Queue handling Excel)
│   ├── Models/                 # Eloquent Model (User, Pelanggan, TugasKunjungan, DetailTugasKunjungan)
│   └── Providers/              # Registrasi Gate otorisasi
├── resources/
│   ├── js/
│   │   ├── Config/             # Konfigurasi koordinat dasar Jambi & konstanta status
│   │   ├── MapCore/            # Inisialisasi peta, manajemen marker & rendering polyline rute
│   │   ├── Pages/              # Entry point peta Admin (adminMap.js) & Petugas (petugasMap.js)
│   │   ├── Services/           # Integrasi API backend & OSRM routing engine
│   │   └── UI/                 # Kontrol UI (Filter offcanvas, search dropdown, loader, popup)
│   └── views/                  # Tampilan Blade + Bootstrap 5 Admin Template
```

---

## 2. Modul Pemetaan GIS (Leaflet JS & OSRM)

Aplikasi ini mengadopsi [Leaflet JS](https://leafletjs.com) sebagai rendering engine peta interaktif. Wilayah operasional default berpusat di **Telanaipura, Jambi** (`lat: -1.6161, lng: 103.583`).

### 2.1. Peta Admin (`adminMap.js`)
- **Tujuan**: Visualisasi makro seluruh sebaran pelanggan PLN.
- **Fitur Utama**:
  - Pengambilan data pelanggan via API endpoint internal (`/api/pelanggans`).
  - **Filter Interaktif**: Pengguna dapat menyaring marker berdasarkan golongan tarif (R1, R2, R3, B1) dan batas rentang daya listrik (VA).
  - **Live Search Autocomplete**: Pencarian cepat nama pelanggan atau IDPEL dengan efek sorot (*highlight*) dan *fly-to* animasi kamera ke koordinat target.
  - **Heatmap Layer**: Visualisasi densitas kepadatan pelanggan di suatu area.

### 2.2. Peta Petugas & Optimasi Rute TSP (`petugasMap.js` & `routeLayer.js`)
- **Tujuan**: Memandu petugas lapangan mengunjungi sejumlah pelanggan yang ditugaskan dengan rute paling efisien.
- **Mekanisme Traveling Salesperson Problem (TSP)**:
  1. Sistem mendeteksi koordinat GPS petugas lapangan secara realtime melalui Geolocation API Browser (`navigator.geolocation.getCurrentPosition`).
  2. Sistem mengumpulkan koordinat seluruh pelanggan yang tercantum dalam penugasan aktif petugas.
  3. Sistem mengirimkan permintaan ke **OSRM Trip API**:
     ```
     GET https://router.project-osrm.org/trip/v1/driving/{petugas_lng,petugas_lat};{p1_lng,p1_lat};{p2_lng,p2_lat}...
     ?roundtrip=false&source=first&destination=last&overview=full&geometries=geojson
     ```
  4. Response GeoJSON OSRM memetakan polyline rute di atas jalan raya nyata dan mengurutkan titik pelanggan menjadi urutan paling efisien (Waypoint 1, Waypoint 2, dst.).
  5. Marker dirender dengan angka nomor urut (1, 2, 3...) dan warna indikator:
     - 🔵 **Biru (`#3051d3`)**: Belum Dikunjungi
     - 🟡 **Kuning (`#ffc107`)**: Sedang Diproses
     - 🟢 **Hijau (`#28a745`)**: Sudah Dikunjungi

---

## 3. Alur Pemrosesan Impor Data Skala Besar

Untuk menangani impor ribuan data pelanggan dari format Excel (`.xlsx`, `.xls`):
1. Pengguna mengunggah berkas via dropzone di antarmuka web.
2. `PelangganController::import` membaca metadata baris menggunakan `PhpSpreadsheet` untuk menghitung total baris.
3. Kunci UUID unik dibuat dan total baris disimpan ke dalam **Laravel Cache** (`import_total_{uuid}`).
4. Pemrosesan baris didelegasikan ke `PelangganImport` yang mengimplementasikan `WithChunkReading` (chunk size 100 baris) dan `ShouldQueue`.
5. Frontend melakukan *polling* berkala ke endpoint `/pelanggan/import/progress/{uuid}` untuk memperbarui progress bar secara halus tanpa membebani memori server (OOM prevention).

---

## 4. Modul Pelaporan & Ekspor Dokumen

- Menggunakan library `barryvdh/laravel-dompdf` untuk konversi dokumen HTML Blade menjadi PDF berkualitas cetak (A4 portrait).
- Didukung filter rentang tanggal dinamis (`tanggal_awal` sampai `tanggal_akhir`) untuk menghasilkan rekapitulasi data kunjungan petugas lapangan secara transparan dan terukur.
