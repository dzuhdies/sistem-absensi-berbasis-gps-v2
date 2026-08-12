<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $this->redirectByRole(Auth::guard($guard)->user()->role);
            }
        }

        return $next($request);
    }

    private function redirectByRole(string $role): Response
    {
        return redirect(match ($role) {
            'admin' => '/admin/dashboard',
            'pegawai' => '/pegawai/rekap-absensi',
            'siswa' => '/siswa/absen',
            'guru' => '/guru/rekap-absensi',
            default => '/',
        });
    }
}
