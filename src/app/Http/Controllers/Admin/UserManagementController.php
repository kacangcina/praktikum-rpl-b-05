<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminActionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,active,suspended,closed'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:5', 'max:50'],
        ]);

        $search = trim($validated['q'] ?? '');
        $status = $validated['status'] ?? 'all';

        $users = User::query()
            ->withCount('recipes')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $like = '%'.$search.'%';
                $query->where('name', 'like', $like)
                    ->orWhere('username', 'like', $like)
                    ->orWhere('email', 'like', $like);
            }))
            ->when($status === 'active', fn ($query) => $query->whereNull('suspended_at')->whereNull('closed_at'))
            ->when($status === 'suspended', fn ($query) => $query->whereNotNull('suspended_at'))
            ->when($status === 'closed', fn ($query) => $query->whereNotNull('closed_at'))
            ->latest('id')
            ->paginate($validated['per_page'] ?? 15);

        return response()->json([
            'users' => $users->getCollection()->map(fn (User $user) => $this->userData($user)),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->is($user), 422, 'Hak akses akun admin yang sedang digunakan tidak dapat diubah.');
        abort_if($user->isClosed(), 422, 'Akun yang sudah ditutup tidak dapat diubah.');

        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'creator', 'admin'])],
        ]);

        $oldRole = $user->role;
        $user->update([
            'role' => $validated['role'],
            'is_verified' => $validated['role'] === 'creator',
        ]);

        $user->notify(new AdminActionNotification([
            'type' => 'role_changed',
            'level' => 'info',
            'title' => 'Hak akses akun diperbarui',
            'message' => "Admin mengubah peran akun kamu dari {$oldRole} menjadi {$validated['role']}.",
            'action_url' => route('profile.show', $user),
            'action_label' => 'Lihat profil',
        ]));

        return response()->json([
            'message' => 'Hak akses pengguna berhasil diperbarui.',
            'user' => $this->userData($user->fresh()->loadCount('recipes')),
        ]);
    }

    public function suspension(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->is($user), 422, 'Akun admin yang sedang digunakan tidak dapat diblokir.');
        abort_if($user->isClosed(), 422, 'Akun yang sudah ditutup tidak dapat diblokir.');

        $validated = $request->validate([
            'suspended' => ['required', 'boolean'],
            'reason' => [
                Rule::requiredIf($request->boolean('suspended')),
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $user->update([
            'suspended_at' => $validated['suspended'] ? now() : null,
            'suspension_reason' => $validated['suspended'] ? trim($validated['reason']) : null,
        ]);

        $user->notify(new AdminActionNotification([
            'type' => $validated['suspended'] ? 'account_suspended' : 'account_restored',
            'level' => $validated['suspended'] ? 'danger' : 'success',
            'title' => $validated['suspended'] ? 'Akun diblokir sementara' : 'Blokir akun telah dibuka',
            'message' => $validated['suspended']
                ? 'Admin memblokir sementara akun kamu. Kamu tidak dapat masuk sampai blokir dibuka.'
                : 'Admin telah membuka blokir akun kamu. Kamu dapat menggunakan CuBu kembali.',
            'reason' => $validated['suspended'] ? trim($validated['reason']) : null,
            'action_url' => $validated['suspended'] ? null : route('profile.show', $user),
            'action_label' => $validated['suspended'] ? null : 'Buka profil',
        ]));

        return response()->json([
            'message' => $validated['suspended']
                ? 'Akun pengguna berhasil diblokir.'
                : 'Blokir akun pengguna berhasil dibuka.',
            'user' => $this->userData($user->fresh()->loadCount('recipes')),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->is($user), 422, 'Akun admin yang sedang digunakan tidak dapat dihapus.');
        abort_if($user->isClosed(), 422, 'Akun pengguna sudah ditutup.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $reason = trim($validated['reason']);
        $user->notify(new AdminActionNotification([
            'type' => 'account_closed',
            'level' => 'danger',
            'title' => 'Akun ditutup oleh admin',
            'message' => 'Akun kamu telah ditutup. Data akun disimpan sebagai arsip dan kamu tidak dapat masuk.',
            'reason' => $reason,
        ]));
        $user->update([
            'closed_at' => now(),
            'closure_reason' => $reason,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return response()->json(['message' => 'Akun pengguna berhasil ditutup dan diarsipkan.']);
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'is_verified' => $user->is_verified,
            'is_suspended' => $user->isSuspended(),
            'is_closed' => $user->isClosed(),
            'suspended_at' => $user->suspended_at,
            'suspension_reason' => $user->suspension_reason,
            'closed_at' => $user->closed_at,
            'closure_reason' => $user->closure_reason,
            'recipes_count' => (int) ($user->recipes_count ?? 0),
            'created_at' => $user->created_at,
        ];
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
