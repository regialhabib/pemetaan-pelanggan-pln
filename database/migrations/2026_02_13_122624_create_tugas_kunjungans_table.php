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
        Schema::create('tugas_kunjungan', function (Blueprint $table) {
            $table->id();

            // User (Petugas Lapangan) yang ditugaskan
            $table->foreignId('id_petugas')
                ->constrained('users')
                ->cascadeOnDelete();

    

            $table->text('keterangan')->nullable();

            $table->enum('status_tugas', [
                'dalam_proses',
                'selesai',
            ])->default('dalam_proses');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_kunjungan');
    }
};
