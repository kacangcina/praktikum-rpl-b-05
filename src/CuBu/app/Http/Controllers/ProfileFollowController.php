<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileFollowController extends Controller
{
    public function store(Request $request, User $user): JsonResponse
    {
        abort_if($request->user()->is($user), 422, 'Kamu tidak dapat mengikuti akun sendiri.');

        $request->user()->following()->syncWithoutDetaching([$user->id]);

        return response()->json(['message' => 'Akun berhasil diikuti.']);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $request->user()->following()->detach($user->id);

        return response()->json(['message' => 'Berhenti mengikuti akun.']);
    }
}
