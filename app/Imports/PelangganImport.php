<?php

namespace App\Imports;

use App\Models\Pelanggan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithChunkReading
};

class PelangganImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    ShouldQueue
{
    protected string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function model(array $row)
    {
        Cache::increment("import_progress_{$this->key}");

        Pelanggan::updateOrCreate(
            ['id_pelanggan' => $row['id_pelanggan']],
            [
                'nama'           => $row['nama'],
                'alamat'         => $row['alamat'],
                'latitude'       => $row['latitude'],
                'longitude'      => $row['longitude'],
                'golongan_tarif' => $row['golongan_tarif'],
                'daya'           => $row['daya'],
            ]
        );

        return null; 
    }

    public function chunkSize(): int
    {
        return 100;
    }
}























/**use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelangganImport implements OnEachRow, WithHeadingRow
{
    
    
     
    protected string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function onRow(Row $row)
    {
        $data = $row->toArray();

        Cache::increment("import_progress_{$this->key}");

        Pelanggan::updateOrCreate(
            ['id_pelanggan' => $data['id_pelanggan']],
            [
                'nama'           => $data['nama'],
                'alamat'         => $data['alamat'],
                'latitude'       => $data['latitude'],
                'longitude'      => $data['longitude'],
                'golongan_tarif' => $data['golongan_tarif'],
                'daya'           => $data['daya'],
            ]
        );
    }
}
 */
