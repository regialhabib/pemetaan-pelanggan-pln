<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTugasKunjungan extends Model
{
    use HasFactory;
    protected $table = 'detail_tugas_kunjungan';
 
    protected $fillable = [
        'id_tugas_kunjungan', 
        'id_pelanggan',          
        'status_kunjungan',
    ];

    // Detail ini milik 1 tugas
    public function tugas()
    {
        return $this->belongsTo(TugasKunjungan::class, 'id_tugas_kunjungan');
    }

    // Detail ini milik 1 pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
}
