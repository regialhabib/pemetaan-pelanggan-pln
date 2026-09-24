# Catatan & Rencana Tindak Lanjut Temuan Teknis

Dokumen ini memuat daftar temuan teknis (*technical findings & code smells*), analisis risiko, serta langkah mitigasi dan perbaikan yang diterapkan pada sistem.

---

## 1. Temuan 1: Inkonsistensi Nilai Enum Status Kunjungan

- **Lokasi**:
  - Migration awal: [`2026_02_13_122853_detail_tugas_kunjungan.php`](../database/migrations/2026_02_13_122853_detail_tugas_kunjungan.php)
  - Controller: [`app/Http/Controllers/TugasKunjunganController.php`](../app/Http/Controllers/TugasKunjunganController.php) (`updateStatus()`)
  - Frontend: [`resources/js/Config/index.js`](../resources/js/Config/index.js) (`STATUS_KUNJUNGAN.DIPROSES`)
- **Masalah**:
  Migration mendefinisikan enum hanya untuk `['belum_dikunjungi', 'sudah_dikunjungi']`. Namun, controller dan frontend JS mengizinkan status perantara `'diproses'`. Pada server MySQL dengan mode strict (`STRICT_TRANS_TABLES`), operasi update ke status `'diproses'` akan memicu exception kegagalan basis data (*Data truncated for column 'status_kunjungan'*).
- **Tindak Lanjut**:
  Membuat migration baru untuk memperbarui definisi enum kolom `status_kunjungan` pada tabel `detail_tugas_kunjungan` menjadi:
  `['belum_dikunjungi', 'diproses', 'sudah_dikunjungi']`.

---

## 2. Temuan 2: Kolom Nomor HP Pelanggan

- **Lokasi**:
  - View Blade: [`resources/views/pelanggan/index.blade.php`](../resources/views/pelanggan/index.blade.php) (Terdapat kolom `<th>No HP</th>` dan pemanggilan data `{{ $pelanggan->no_hp }}`)
  - Model: [`app/Models/Pelanggan.php`](../app/Models/Pelanggan.php)
  - Controller: [`app/Http/Controllers/PelangganController.php`](../app/Http/Controllers/PelangganController.php)
- **Masalah**:
  Kolom `no_hp` belum terdapat di skema tabel database `pelanggans`, belum terdaftar pada `$fillable` model, serta belum memiliki input form pada Modal Tambah maupun Modal Edit pelanggan.
- **Tindak Lanjut**:
  1. Membuat migration untuk menambahkan kolom `no_hp` (tipe `VARCHAR(20)`, `NULLABLE`) setelah kolom `nama` pada tabel `pelanggans`.
  2. Mendaftarkan `'no_hp'` ke dalam `$fillable` pada model `Pelanggan`.
  3. Menambahkan validasi `no_hp` pada `PelangganController::store` dan `PelangganController::update`.
  4. Menambahkan input field `no_hp` pada Modal Tambah dan Modal Edit di tampilan Blade.
  5. Memetakan kolom `no_hp` pada class `PelangganImport`.

---

## 3. Temuan 3: Konfigurasi Antrian Impor Excel (Queue Worker)

- **Lokasi**:
  - Konfigurasi `.env`: `QUEUE_CONNECTION=sync`
  - Class Import: [`app/Imports/PelangganImport.php`](../app/Imports/PelangganImport.php)
- **Masalah**:
  Fitur impor data menggunakan `Excel::queueImport` dan mengimplementasikan `ShouldQueue`. Karena `QUEUE_CONNECTION=sync`, pemrosesan file Excel ribuan baris tetap dieksekusi secara sinkron di thread HTTP request yang sama, sehingga indikator progress bar polling tidak berjalan secara optimal (langsung lompat ke selesai).
- **Rekomendasi Operasional**:
  Untuk deployment server produksi yang menangani file ratusan ribu baris:
  1. Ubah konfigurasi di `.env` menjadi `QUEUE_CONNECTION=database` atau `QUEUE_CONNECTION=redis`.
  2. Jalankan background worker dengan supervisor: `php artisan queue:work`.
