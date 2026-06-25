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
            'title' => $approved ? 'Akun creator disetujui' : 'Pengajuan creator ditolak',
            'message' => $approved
                ? 'Akun kamu telah diverifikasi. Kamu sekarang dapat mengunggah video kelas memasak.'
                : 'Pengajuan creator ditolak: '.$this->verification->rejection_reason,
            'status' => $this->verification->status,
        ];
    }
}
