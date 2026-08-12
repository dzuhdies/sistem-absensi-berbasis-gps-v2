<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class);
    }

    public function pegawaiProfile()
    {
        return $this->hasOne(PegawaiProfile::class);
    }
    public function getAuthIdentifierName()
    {
        return 'username';
    }
    public function siswaYangDiawasi()
    {
        return $this->belongsToMany(SiswaProfile::class, 'guru_siswa', 'guru_id', 'siswa_id');
    }
}
