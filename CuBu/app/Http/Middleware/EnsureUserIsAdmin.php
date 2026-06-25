<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            abort(403, 'Halaman ini hanya dapat diakses oleh admin.');
        }

        return redirect()
            ->route('recipes.index')
            ->with('error', 'Halaman admin hanya dapat diakses oleh akun admin.');
    }
}
