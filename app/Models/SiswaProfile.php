<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'asal_pkl',
        'gedung_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }
    public function guru()
    {
        return $this->belongsToMany(User::class, 'guru_siswa', 'siswa_id', 'guru_id');
    }
}
