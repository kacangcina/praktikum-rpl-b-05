<?php

namespace App\Notifications;

use App\Models\CreatorVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CreatorVerificationReviewed extends Notification
{
    use Queueable;

    public function __construct(private readonly CreatorVerification $verification) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $approved = $this->verification->status === 'approved';

        return [
            'type' => $approved ? 'creator_approved' : 'creator_rejected',
            'level' => $approved ? 'success' : 'warning',
            'title' => $approved ? 'Akun creator disetujui' : 'Pengajuan creator ditolak',
            'message' => $approved
                ? 'Akun kamu telah diverifikasi. Kamu sekarang dapat mengunggah video kelas memasak.'
                : 'Pengajuan creator ditolak: '.$this->verification->rejection_reason
                    .'. Perbaiki dokumen atau informasi yang diminta, lalu ajukan ulang melalui halaman verifikasi creator.',
            'status' => $this->verification->status,
            'reason' => $approved ? null : $this->verification->rejection_reason,
            'action_url' => $approved ? route('profile.me') : route('creator.apply'),
            'action_label' => $approved ? 'Buka profil' : 'Ajukan ulang',
        ];
    }
}
