<?php

namespace App\Models;

use App\Models\DetailTugasKunjungan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasKunjungan extends Model
{
    use HasFactory;

    protected $table = 'tugas_kunjungan';
    public function detailTugas()
    {
        return $this->hasMany(DetailTugasKunjungan::class, 'id_tugas_kunjungan');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }
}
