<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        return redirect()->route('profile.show', $request->user());
    }

    public function show(User $user)
    {
        $user->load('latestCreatorVerification');

        $recipes = $user->recipes()
            ->with(['ingredients', 'video'])
            ->latest('published_at')
            ->paginate(9);

        $notifications = auth()->id() === $user->id
            ? $user->notifications()->latest()->limit(5)->get()
            : collect();

        return view('profiles.show', compact('user', 'recipes', 'notifications'));
    }

    public function edit(Request $request)
    {
        return view('profiles.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'username.unique' => 'Username sudah digunakan pengguna lain.',
            'avatar.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        $user->update($validated);

        return redirect()
            ->route('profile.show', $user)
            ->with('status', 'Profil berhasil diperbarui.');
    }
}
