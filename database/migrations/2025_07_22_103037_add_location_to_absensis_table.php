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
        Schema::table('absensis', function (Blueprint $table) {
            // Tambahkan kolom ini
            $table->decimal('lokasi_masuk_lat', 10, 8)->nullable()->after('foto_masuk');
            $table->decimal('lokasi_masuk_long', 11, 8)->nullable()->after('lokasi_masuk_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['lokasi_masuk_lat', 'lokasi_masuk_long']);
        });
    }
};
