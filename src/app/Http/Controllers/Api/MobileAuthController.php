<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MobileAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'device_name' => ['nullable', 'string', 'max:100'],
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

        return $this->tokenResponse($user, $validated['device_name'] ?? null, 'Akun berhasil dibuat.', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau kata sandi tidak sesuai.',
                'errors' => [
                    'email' => ['Email atau kata sandi tidak sesuai.'],
                ],
            ], 422);
        }

        if ($user->isClosed()) {
            $message = 'Akun kamu telah ditutup oleh admin'
                .($user->closure_reason ? ': '.$user->closure_reason : '.');

            return response()->json([
                'message' => $message,
                'errors' => ['email' => [$message]],
            ], 403);
        }

        if ($user->isSuspended()) {
            $message = 'Akun kamu sedang diblokir'
                .($user->suspension_reason ? ': '.$user->suspension_reason : '.');

            return response()->json([
                'message' => $message,
                'errors' => ['email' => [$message]],
            ], 403);
        }

        return $this->tokenResponse($user, $validated['device_name'] ?? null, 'Berhasil masuk.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    private function tokenResponse(
        User $user,
        ?string $deviceName,
        string $message,
        int $status = 200,
    ): JsonResponse {
        $token = $user->createToken($deviceName ?: 'CuBu Android')->plainTextToken;

        return response()->json([
            'message' => $message,
            'token' => $token,
            'user_id' => $user->id,
        ], $status);
    }
}
