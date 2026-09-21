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
        Schema::create('detail_tugas_kunjungan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_tugas_kunjungan')
                ->constrained('tugas_kunjungan')
                ->cascadeOnDelete();

            $table->foreignId('id_pelanggan')
                ->constrained('pelanggans')
                ->cascadeOnDelete();

            $table->enum('status_kunjungan', [
                'belum_dikunjungi',
                'sudah_dikunjungi',
            ])->default('belum_dikunjungi');


            $table->timestamps();
            // Mencegah 1 pelanggan dimasukkan 2x dalam 1 tugas
           // $table->unique(['id_tugas_kunjungan', 'id_pelanggan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_tugas_kunjungan');
    }
};
