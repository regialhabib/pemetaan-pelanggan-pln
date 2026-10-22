<?php

namespace App\Models;

use App\Enums\StatusTugas;
use Database\Factories\TugasKunjunganFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('tugas_kunjungan')]
#[Fillable(['id_petugas', 'keterangan', 'status_tugas'])]
class TugasKunjungan extends Model
{
    /** @use HasFactory<TugasKunjunganFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status_tugas' => StatusTugas::class,
        ];
    }

    /**
     * @return HasMany<DetailTugasKunjungan, $this>
     */
    public function detailTugas(): HasMany
    {
        return $this->hasMany(DetailTugasKunjungan::class, 'id_tugas_kunjungan');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }
}
