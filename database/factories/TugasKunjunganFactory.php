<?php

namespace Database\Factories;

use App\Models\TugasKunjungan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TugasKunjungan>
 */
class TugasKunjunganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_petugas' => User::factory(),
            'keterangan' => fake()->sentence(),
        ];
    }
}
