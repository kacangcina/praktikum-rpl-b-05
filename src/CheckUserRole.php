<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah terautentikasi
        if (!Auth::check() || !($user = Auth::user())) {
            // Jika tidak, tolak akses. Untuk API, kembalikan JSON.
            return new JsonResponse(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        // 2. Periksa apakah peran pengguna ada di dalam daftar peran yang diizinkan
        // Kita menggunakan $user->role->value karena 'role' di-cast sebagai Enum di model User.
        if (in_array($user->role->value, $roles)) {
            // 3. Jika peran cocok, lanjutkan ke controller
            return $next($request);
        }

        // 4. Jika tidak, kembalikan error 403 Forbidden
        return new JsonResponse(['message' => 'Forbidden: You do not have the required role to access this resource.'], Response::HTTP_FORBIDDEN);
    }
}