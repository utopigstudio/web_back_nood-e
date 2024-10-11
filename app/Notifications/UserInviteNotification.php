<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInviteNotification extends Notification
{
    use Queueable;

    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hola!')
            ->line('Has estat convidat com a col·laborador.')
            ->action('Accepta la invitació', $this->url)
            ->line('Gràcies per utilitzar la nostra aplicació!')
            ->salutation('Salutacions,<br>'.config('app.name'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
