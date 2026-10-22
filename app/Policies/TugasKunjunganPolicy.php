<?php

namespace App\Policies;

use App\Models\TugasKunjungan;
use App\Models\User;

class TugasKunjunganPolicy
{
    /**
     * Admin boleh melihat semua tugas, petugas hanya tugas miliknya.
     */
    public function view(User $user, TugasKunjungan $tugasKunjungan): bool
    {
        return $user->isAdmin() || $this->owns($user, $tugasKunjungan);
    }

    /**
     * Memperbarui status kunjungan pada tugas ini.
     */
    public function update(User $user, TugasKunjungan $tugasKunjungan): bool
    {
        return $user->isAdmin() || $this->owns($user, $tugasKunjungan);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    private function owns(User $user, TugasKunjungan $tugasKunjungan): bool
    {
        return (int) $tugasKunjungan->id_petugas === $user->id;
    }
}
