<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'role',
        'aksi',
        'keterangan',
        'ip_address',
        'user_agent',
    ];

    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}

}
