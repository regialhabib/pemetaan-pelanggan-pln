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
        if (!Schema::hasColumn('pelanggans', 'no_hp')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->string('no_hp', 20)->nullable()->after('nama');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pelanggans', 'no_hp')) {
            Schema::table('pelanggans', function (Blueprint $table) {
                $table->dropColumn('no_hp');
            });
        }
    }
};
