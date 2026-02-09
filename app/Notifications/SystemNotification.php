<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected array $data;

    public function __construct(array $data)
    {
        /*
            Expected keys:
            type, title, message, url, icon, extra
        */
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'    => $this->data['type'],
            'title'   => $this->data['title'],
            'message' => $this->data['message'],
            'url'     => $this->data['url'] ?? null,
            'icon'    => $this->data['icon'] ?? 'bell',
            'extra'   => $this->data['extra'] ?? [],
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
