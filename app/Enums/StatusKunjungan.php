<?php

namespace App\Enums;

enum StatusKunjungan: string
{
    case BelumDikunjungi = 'belum_dikunjungi';
    case Diproses = 'diproses';
    case SudahDikunjungi = 'sudah_dikunjungi';

    /**
     * Status berikutnya dalam alur kunjungan, atau null jika sudah selesai.
     */
    public function next(): ?self
    {
        return match ($this) {
            self::BelumDikunjungi => self::Diproses,
            self::Diproses => self::SudahDikunjungi,
            self::SudahDikunjungi => null,
        };
    }

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }
}
