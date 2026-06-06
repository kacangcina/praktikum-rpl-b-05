<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorVerification;
use App\Notifications\CreatorVerificationReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreatorVerificationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $status = $request->query('status', 'pending');
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        $status = in_array($status, $allowedStatuses, true) ? $status : 'pending';

        $verifications = CreatorVerification::with('user')
            ->where('status', $status)
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        $counts = CreatorVerification::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.creator-verifications.index', compact('verifications', 'status', 'counts'));
    }

    public function show(Request $request, CreatorVerification $verification)
    {
        $this->authorizeAdmin($request);
        $verification->load(['user', 'reviewer']);

        return view('admin.creator-verifications.show', compact('verification'));
    }

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

        return redirect()
            ->route('admin.creator-verifications.index')
            ->with('status', 'Pengajuan creator ditolak.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
