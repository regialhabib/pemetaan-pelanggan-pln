<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggans = [
            // R1 - Residential 1 (Rumah Tangga)
            ['id_pelanggan' => 'PLN001', 'nama' => 'Budi Santoso', 'alamat' => 'Jl. Jend. Sudirman No. 45, Telanaipura, Jambi', 'latitude' => -1.6100, 'longitude' => 103.6100, 'golongan_tarif' => 'R1', 'daya' => 900],
            ['id_pelanggan' => 'PLN002', 'nama' => 'Siti Nurhaliza', 'alamat' => 'Jl. Prof. M. Yamin No. 12, Telanaipura, Jambi', 'latitude' => -1.6120, 'longitude' => 103.6125, 'golongan_tarif' => 'R1', 'daya' => 1300],
            ['id_pelanggan' => 'PLN003', 'nama' => 'Ahmad Fauzi', 'alamat' => 'Jl. Sultan Thaha No. 78, Telanaipura, Jambi', 'latitude' => -1.6085, 'longitude' => 103.6080, 'golongan_tarif' => 'R1', 'daya' => 2200],
            ['id_pelanggan' => 'PLN004', 'nama' => 'Dewi Lestari', 'alamat' => 'Jl. Gatot Subroto No. 23, Telanaipura, Jambi', 'latitude' => -1.6132, 'longitude' => 103.6098, 'golongan_tarif' => 'R1', 'daya' => 900],
            ['id_pelanggan' => 'PLN005', 'nama' => 'Rudi Hartono', 'alamat' => 'Jl. Kol. Abunjani No. 56, Telanaipura, Jambi', 'latitude' => -1.6055, 'longitude' => 103.6055, 'golongan_tarif' => 'R1', 'daya' => 1300],
            ['id_pelanggan' => 'PLN006', 'nama' => 'Maya Sari', 'alamat' => 'Jl. Kapt. Pattimura No. 34, Telanaipura, Jambi', 'latitude' => -1.6078, 'longitude' => 103.6112, 'golongan_tarif' => 'R1', 'daya' => 2200],
            ['id_pelanggan' => 'PLN007', 'nama' => 'Iwan Setiawan', 'alamat' => 'Jl. Sultan Agung No. 89, Telanaipura, Jambi', 'latitude' => -1.6140, 'longitude' => 103.6070, 'golongan_tarif' => 'R1', 'daya' => 1300],

            // R2 - Residential 2 (Usaha Kecil)
            ['id_pelanggan' => 'PLN008', 'nama' => 'PT Indah Sejahtera', 'alamat' => 'Jl. Raden Mattaher No. 23, Telanaipura, Jambi', 'latitude' => -1.6060, 'longitude' => 103.6130, 'golongan_tarif' => 'R2', 'daya' => 3500],
            ['id_pelanggan' => 'PLN009', 'nama' => 'CV Makmur Jaya', 'alamat' => 'Jl. Teuku Umar No. 67, Telanaipura, Jambi', 'latitude' => -1.6092, 'longitude' => 103.6145, 'golongan_tarif' => 'R2', 'daya' => 5500],
            ['id_pelanggan' => 'PLN010', 'nama' => 'UD Berkah', 'alamat' => 'Jl. Diponegoro No. 45, Telanaipura, Jambi', 'latitude' => -1.6115, 'longitude' => 103.6060, 'golongan_tarif' => 'R2', 'daya' => 4400],
            ['id_pelanggan' => 'PLN011', 'nama' => 'Toko Sumber Rezeki', 'alamat' => 'Jl. HOS Cokroaminoto No. 78, Telanaipura, Jambi', 'latitude' => -1.6048, 'longitude' => 103.6092, 'golongan_tarif' => 'R2', 'daya' => 3500],
            ['id_pelanggan' => 'PLN012', 'nama' => 'Warung Makan Sederhana', 'alamat' => 'Jl. Prof. Dr. Sri Soedewi No. 12, Telanaipura, Jambi', 'latitude' => -1.6080, 'longitude' => 103.6048, 'golongan_tarif' => 'R2', 'daya' => 5500],
            ['id_pelanggan' => 'PLN013', 'nama' => 'Minimarket Sejahtera', 'alamat' => 'Jl. K.H. Ahmad Dahlan No. 34, Telanaipura, Jambi', 'latitude' => -1.6105, 'longitude' => 103.6155, 'golongan_tarif' => 'R2', 'daya' => 4400],

            // R3 - Residential 3 (Usaha Besar/Komersial)
            ['id_pelanggan' => 'PLN014', 'nama' => 'Hotel Grand Jambi', 'alamat' => 'Jl. Jend. Sudirman No. 100, Telanaipura, Jambi', 'latitude' => -1.6050, 'longitude' => 103.6075, 'golongan_tarif' => 'R3', 'daya' => 6600],
            ['id_pelanggan' => 'PLN015', 'nama' => 'Apartemen Telanaipura', 'alamat' => 'Jl. Sultan Thaha No. 56, Telanaipura, Jambi', 'latitude' => -1.6095, 'longitude' => 103.6090, 'golongan_tarif' => 'R3', 'daya' => 7700],
            ['id_pelanggan' => 'PLN016', 'nama' => 'Ruko Pasar Telanaipura', 'alamat' => 'Jl. Prof. M. Yamin No. 89, Telanaipura, Jambi', 'latitude' => -1.6125, 'longitude' => 103.6110, 'golongan_tarif' => 'R3', 'daya' => 11000],
            ['id_pelanggan' => 'PLN017', 'nama' => 'Gedung Perkantoran Telanaipura', 'alamat' => 'Jl. Gatot Subroto No. 234, Telanaipura, Jambi', 'latitude' => -1.6070, 'longitude' => 103.6135, 'golongan_tarif' => 'R3', 'daya' => 9900],
            ['id_pelanggan' => 'PLN018', 'nama' => 'Mall Jambi City', 'alamat' => 'Jl. Jend. Sudirman No. 150, Telanaipura, Jambi', 'latitude' => -1.6040, 'longitude' => 103.6050, 'golongan_tarif' => 'R3', 'daya' => 13200],

            // INDUSTRI - Industrial
            ['id_pelanggan' => 'PLN019', 'nama' => 'PT Industri Tekstil Jambi', 'alamat' => 'Jl. Industri No. 100, Telanaipura, Jambi', 'latitude' => -1.6180, 'longitude' => 103.6020, 'golongan_tarif' => 'INDUSTRI', 'daya' => 25000],
            ['id_pelanggan' => 'PLN020', 'nama' => 'PT Pabrik Baja Jambi', 'alamat' => 'Jl. Raya Telanaipura Km 5, Telanaipura, Jambi', 'latitude' => -1.6200, 'longitude' => 103.5985, 'golongan_tarif' => 'INDUSTRI', 'daya' => 50000],
            ['id_pelanggan' => 'PLN021', 'nama' => 'PT Manufacturing Jambi Jaya', 'alamat' => 'Jl. Kawasan Industri No. 200, Telanaipura, Jambi', 'latitude' => -1.6155, 'longitude' => 103.6005, 'golongan_tarif' => 'INDUSTRI', 'daya' => 35000],
            ['id_pelanggan' => 'PLN022', 'nama' => 'PT Industri Kimia Jambi', 'alamat' => 'Jl. Raya Telanaipura Km 7, Telanaipura, Jambi', 'latitude' => -1.6220, 'longitude' => 103.5950, 'golongan_tarif' => 'INDUSTRI', 'daya' => 75000],
            ['id_pelanggan' => 'PLN023', 'nama' => 'PT Pabrik Makanan Jambi Sejahtera', 'alamat' => 'Jl. Industri Pangan No. 150, Telanaipura, Jambi', 'latitude' => -1.6170, 'longitude' => 103.6035, 'golongan_tarif' => 'INDUSTRI', 'daya' => 40000],
            ['id_pelanggan' => 'PLN024', 'nama' => 'PT Industri Plastik Jambi', 'alamat' => 'Jl. Industri No. 300, Telanaipura, Jambi', 'latitude' => -1.6195, 'longitude' => 103.5995, 'golongan_tarif' => 'INDUSTRI', 'daya' => 60000],
            ['id_pelanggan' => 'PLN025', 'nama' => 'PT Pabrik Kertas Jambi', 'alamat' => 'Jl. Raya Telanaipura Km 8, Telanaipura, Jambi', 'latitude' => -1.6235, 'longitude' => 103.5930, 'golongan_tarif' => 'INDUSTRI', 'daya' => 80000],
            ['id_pelanggan' => 'PLN026', 'nama' => 'PT Industri Otomotif Jambi', 'alamat' => 'Jl. Kawasan Industri Baru No. 250, Telanaipura, Jambi', 'latitude' => -1.6215, 'longitude' => 103.5965, 'golongan_tarif' => 'INDUSTRI', 'daya' => 90000],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}
