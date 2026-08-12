<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
public function handle($request, Closure $next, $role)
{
    if (!auth()->check()) {
        return redirect('/login');
    }

    if (auth()->user()->role === $role) {
        return $next($request);
    }

    return redirect(match (auth()->user()->role) {
        'admin' => '/admin/dashboard',
        'pegawai' => '/pegawai/rekap-absensi',
        'siswa' => '/siswa/absen',
        'guru' => '/guru/rekap-absensi',
        default => '/login',
    });
}

}
