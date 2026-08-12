<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;


class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
        
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'remember' => 'nullable|boolean',
        ]);

        $remember = $request->boolean('remember');
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            LogHelper::log($request, 'Login', 'User berhasil login');


            $role = Auth::user()->role;
            return match ($role) {
                'admin' => redirect('/admin/dashboard'),
                'pegawai' => redirect('/pegawai/rekap-absensi'),
                'siswa' => redirect('/siswa/absen'),
                'guru' => redirect('/guru/rekap-absensi'),
                default => redirect('/'),
            };
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }


    public function logout(Request $request)
    {
        LogHelper::log($request, 'Logout', 'User logout dari sistem');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
