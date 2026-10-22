<?php

namespace App\Imports;

use App\Models\Pelanggan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements ShouldQueue, ToModel, WithChunkReading, WithHeadingRow
{
    public function __construct(protected string $key) {}

    /**
     * Upsert per baris berdasarkan `id_pelanggan`, lalu catat progres di cache.
     *
     * @param  array<string, mixed>  $row
     */
    public function model(array $row): Model|array|null
    {
        Cache::increment("import_progress_{$this->key}");

        Pelanggan::updateOrCreate(
            ['id_pelanggan' => $row['id_pelanggan']],
            [
                'nama' => $row['nama'],
                'no_hp' => $row['no_hp'] ?? null,
                'alamat' => $row['alamat'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'golongan_tarif' => $row['golongan_tarif'],
                'daya' => $row['daya'],
            ]
        );

        return null;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
