<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB; // Pastikan ini ditambahkan
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus constraint yang lama
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');

        // Tambahkan constraint baru yang sudah menyertakan 'guru'
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa', 'pegawai', 'admin', 'guru'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fungsi ini untuk membatalkan migrasi (rollback)
        // Hapus constraint yang baru
        DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');

        // Kembalikan constraint ke versi lama
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('siswa', 'pegawai', 'admin'))");
    }
};