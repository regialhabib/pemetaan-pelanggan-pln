<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_tugas_kunjungan', function (Blueprint $table) {
            $table->enum('status_kunjungan', ['belum_dikunjungi', 'diproses', 'sudah_dikunjungi'])
                ->default('belum_dikunjungi')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_tugas_kunjungan', function (Blueprint $table) {
            $table->enum('status_kunjungan', ['belum_dikunjungi', 'sudah_dikunjungi'])
                ->default('belum_dikunjungi')
                ->change();
        });
    }
};
