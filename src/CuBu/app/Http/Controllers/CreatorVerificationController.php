<?php

namespace App\Http\Controllers;

use App\Models\CreatorVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreatorVerificationController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        abort_if($user->canUploadVideos(), 422, 'Akun kamu sudah menjadi creator terverifikasi.');
        abort_if(
            $user->creatorVerifications()->where('status', 'pending')->exists(),
            422,
            'Pengajuan kamu masih menunggu peninjauan admin.',
        );

        $validated = $request->validate([
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'portfolio_url' => ['nullable', 'url', 'max:1000'],
            'notes' => ['required', 'string', 'max:2000'],
        ], [
            'document.required' => 'Dokumen pendukung wajib diunggah.',
            'document.mimes' => 'Dokumen harus berformat PDF, JPG, atau PNG.',
            'document.max' => 'Ukuran dokumen maksimal 10 MB.',
        ]);

        $documentPath = $request->file('document')->store('creator-documents');

        CreatorVerification::create([
            'user_id' => $user->id,
            'document_path' => $documentPath,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'notes' => $validated['notes'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pengajuan creator berhasil dikirim.'], 201);
        }

        return redirect()
            ->route('profile.me')
            ->with('status', 'Pengajuan creator berhasil dikirim dan sedang ditinjau admin.');
    }

    public function download(Request $request, CreatorVerification $verification)
    {
        abort_unless(
            $request->user()->id === $verification->user_id || $request->user()->isAdmin(),
            403,
        );
        abort_unless(Storage::exists($verification->document_path), 404);

        return Storage::download($verification->document_path);
    }
}
