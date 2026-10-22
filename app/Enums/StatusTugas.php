<?php

namespace App\Enums;

enum StatusTugas: string
{
    case DalamProses = 'dalam_proses';
    case Selesai = 'selesai';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }
}
