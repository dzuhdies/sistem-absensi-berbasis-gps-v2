<?php

namespace App\Helpers;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogHelper
{
    public static function log(Request $request, $aksi, $keterangan = null)
    {
        LogActivity::create([
            'user' => Auth::check() ? Auth::user()->username : null,
            'role'       => Auth::check() ? Auth::user()->role : null,
            'aksi'       => $aksi,
            'keterangan' => $keterangan,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
