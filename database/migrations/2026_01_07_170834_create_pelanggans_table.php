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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan')->unique();
            $table->string('nama', 255);
            $table->text('alamat');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->enum('golongan_tarif', ['R1', 'R2', 'R3', 'INDUSTRI']);
            $table->integer('daya'); // in VA
            $table->timestamps();

            // Indexes for performance
            $table->index('golongan_tarif', 'idx_golongan_tarif');
            $table->index('daya', 'idx_daya');
            $table->index(['latitude', 'longitude'], 'idx_lat_lng');
            $table->index('id_pelanggan', 'idx_id_pelanggan');
            $table->index('nama', 'idx_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
