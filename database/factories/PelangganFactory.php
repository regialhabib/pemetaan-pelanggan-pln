<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pelanggan>
 */
class PelangganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_pelanggan' => 'PLN'.fake()->unique()->numerify('#####'),
            'nama' => fake()->name(),
            'no_hp' => '08'.fake()->numerify('##########'),
            'alamat' => fake()->streetAddress().', Kota Jambi',
            'latitude' => fake()->randomFloat(6, -1.65, -1.55),
            'longitude' => fake()->randomFloat(6, 103.53, 103.65),
            'golongan_tarif' => fake()->randomElement(['R1', 'R2', 'R3', 'INDUSTRI']),
            'daya' => fake()->randomElement([450, 900, 1300, 2200, 3500]),
        ];
    }
}
