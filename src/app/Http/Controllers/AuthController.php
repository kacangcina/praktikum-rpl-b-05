<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'Email sudah digunakan. Silakan gunakan email lain atau masuk ke akun kamu.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['username'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'user',
            'is_verified' => false,
        ]);

        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Akun berhasil dibuat.', 'user_id' => $user->id], 201);
        }

        return redirect()
            ->route('profile.me')
            ->with('status', 'Akun berhasil dibuat. Kamu sudah bisa membuat resep.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Berhasil masuk.']);
        }

        return redirect()->intended(route('recipes.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Berhasil keluar.']);
        }

        return redirect()->route('recipes.index');
    }
}
