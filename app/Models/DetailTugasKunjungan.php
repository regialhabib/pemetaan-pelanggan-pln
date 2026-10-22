<?php

namespace App\Models;

use App\Enums\StatusKunjungan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('detail_tugas_kunjungan')]
#[Fillable(['id_tugas_kunjungan', 'id_pelanggan', 'status_kunjungan'])]
class DetailTugasKunjungan extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_kunjungan' => StatusKunjungan::class,
        ];
    }

    /**
     * @return BelongsTo<TugasKunjungan, $this>
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(TugasKunjungan::class, 'id_tugas_kunjungan');
    }

    /**
     * @return BelongsTo<Pelanggan, $this>
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }
}
