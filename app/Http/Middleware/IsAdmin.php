<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna sudah login DAN memiliki peran 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Jika ya, izinkan permintaan untuk melanjutkan
            return $next($request);
        }

        // Jika tidak, tolak akses dengan pesan error 403 (Forbidden)
        return response()->json(['message' => 'Akses ditolak. Hanya untuk admin.'], 403);
    }
}
