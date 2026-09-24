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
        $faker = \Faker\Factory::create('id_ID');

        // Rentang koordinat Kota Jambi (sekitar Telanaipura dan sekitarnya)
        $minLat = -1.6350;
        $maxLat = -1.5900;
        $minLng = 103.5700;
        $maxLng = 103.6250;

        $golonganTarif = ['R1', 'R2', 'R3', 'INDUSTRI'];
        $dayaOptions = [
            'R1' => [450, 900, 1300, 2200],
            'R2' => [3500, 4400, 5500],
            'R3' => [6600, 7700, 11000, 13200],
            'INDUSTRI' => [25000, 35000, 50000, 100000]
        ];

        for ($i = 1; $i <= 150; $i++) {
            $tarif = $faker->randomElement($golonganTarif);
            $daya = $faker->randomElement($dayaOptions[$tarif]);
            
            // Format ID Pelanggan: PLN + 5 digit
            $idPelanggan = 'PLN' . str_pad($i, 5, '0', STR_PAD_LEFT);
            
            // Generate random lat/lng in bounding box
            $lat = $minLat + ($faker->randomFloat(5, 0, 1) * ($maxLat - $minLat));
            $lng = $minLng + ($faker->randomFloat(5, 0, 1) * ($maxLng - $minLng));

            // Generate fake phone number starting with 08
            $noHp = '08' . $faker->randomNumber(8, true);

            Pelanggan::create([
                'id_pelanggan' => $idPelanggan,
                'nama' => $faker->name,
                'alamat' => $faker->streetAddress . ', Kota Jambi',
                'no_hp' => $noHp,
                'latitude' => $lat,
                'longitude' => $lng,
                'golongan_tarif' => $tarif,
                'daya' => $daya
            ]);
        }
    }
}
