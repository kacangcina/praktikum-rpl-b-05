<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isSuspended() || $request->user()?->isClosed()) {
            $message = $request->user()->isClosed()
                ? 'Akun kamu telah ditutup oleh admin.'
                : 'Akun kamu sedang diblokir. Hubungi admin untuk informasi lebih lanjut.';

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, $message);
        }

        return $next($request);
    }
}
