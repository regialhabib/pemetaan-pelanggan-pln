<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE detail_tugas_kunjungan MODIFY COLUMN status_kunjungan ENUM('belum_dikunjungi', 'diproses', 'sudah_dikunjungi') NOT NULL DEFAULT 'belum_dikunjungi'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE detail_tugas_kunjungan MODIFY COLUMN status_kunjungan ENUM('belum_dikunjungi', 'sudah_dikunjungi') NOT NULL DEFAULT 'belum_dikunjungi'");
    }
};
