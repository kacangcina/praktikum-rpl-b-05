<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminActionNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly array $payload) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->payload['type'],
            'level' => $this->payload['level'] ?? 'info',
            'title' => $this->payload['title'],
            'message' => $this->payload['message'],
            'reason' => $this->payload['reason'] ?? null,
            'action_url' => $this->payload['action_url'] ?? null,
            'action_label' => $this->payload['action_label'] ?? null,
            'subject' => $this->payload['subject'] ?? null,
        ];
    }
}
