<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorVerification;
use App\Notifications\CreatorVerificationReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreatorVerificationController extends Controller
{
    public function approve(Request $request, CreatorVerification $verification)
    {
        $this->authorizeAdmin($request);
        abort_unless($verification->status === 'pending', 422, 'Pengajuan ini sudah ditinjau.');

        DB::transaction(function () use ($request, $verification): void {
            $verification->update([
                'status' => 'approved',
                'rejection_reason' => null,
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
            ]);

            $verification->user->update([
                'role' => 'creator',
                'is_verified' => true,
            ]);

            $verification->user->notify(new CreatorVerificationReviewed($verification));
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pengajuan creator disetujui.']);
        }

        return redirect()
            ->route('admin.creator-verifications.index')
            ->with('status', 'Pengajuan creator disetujui.');
    }

    public function reject(Request $request, CreatorVerification $verification)
    {
        $this->authorizeAdmin($request);
        abort_unless($verification->status === 'pending', 422, 'Pengajuan ini sudah ditinjau.');

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $verification, $validated): void {
            $verification->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'reviewed_at' => now(),
                'reviewed_by' => $request->user()->id,
            ]);

            $verification->user->update([
                'role' => 'user',
                'is_verified' => false,
            ]);

            $verification->user->notify(new CreatorVerificationReviewed($verification));
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pengajuan creator ditolak.']);
        }

        return redirect()
            ->route('admin.creator-verifications.index')
            ->with('status', 'Pengajuan creator ditolak.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
