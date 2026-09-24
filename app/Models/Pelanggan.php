<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';

    protected $fillable = [
        'id',
        'id_pelanggan',
        'nama',
        'no_hp',
        'alamat',
        'latitude',
        'longitude',
        'golongan_tarif',
        'daya'
    ];
}
