<?php

namespace App\Models;

use Database\Factories\PelangganFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('pelanggans')]
#[Fillable([
    'id_pelanggan',
    'nama',
    'no_hp',
    'alamat',
    'latitude',
    'longitude',
    'golongan_tarif',
    'daya',
])]
class Pelanggan extends Model
{
    /** @use HasFactory<PelangganFactory> */
    use HasFactory;

    /**
     * @return HasMany<DetailTugasKunjungan, $this>
     */
    public function detailTugas(): HasMany
    {
        return $this->hasMany(DetailTugasKunjungan::class, 'id_pelanggan');
    }
}
