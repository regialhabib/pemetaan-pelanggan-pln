<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom `role` sebelumnya ditambahkan manual di database produksi,
     * jadi migrasi ini hanya menambahkannya bila belum ada.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('petugas')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak menghapus kolom: pada database lama kolom ini bukan dibuat oleh migrasi ini.
    }
};
