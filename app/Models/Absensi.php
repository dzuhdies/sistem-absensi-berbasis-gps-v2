<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Absensi extends Model
{
    protected $casts = [
        'izin' => 'boolean',
        'status_kehadiran' => 'boolean',
        'status_kerja' => 'boolean',
    ];

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'lokasi_masuk_lat',
        'lokasi_masuk_long',
        'jam_pulang',
        'status_kerja',
        'status_kehadiran',
        'foto_masuk',
        'foto_keluar',
        'keterangan_izin',
        'jenis_izin_id',
        'file_izin',
        'izin',
        'status_ketepatan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(SiswaProfile::class, 'siswa_id');
    }

    public function jenisIzin(): BelongsTo
    {
        return $this->belongsTo(JenisIzin::class, 'jenis_izin_id');
    }

    public function getDurasiKerjaAttribute(): ?string
    {
        if ($this->jam_pulang && $this->jam_masuk) {
            $masuk = Carbon::parse($this->jam_masuk);
            $pulang = Carbon::parse($this->jam_pulang);

            $durasi = $pulang->diff($masuk);
            return $durasi->format('%h jam %i menit');
        }

        return null;
    }
}
