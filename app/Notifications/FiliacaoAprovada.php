<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FiliacaoAprovada extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly array $context)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Filiação aprovada no PCT')
            ->view('emails.filiacao-aprovada', $this->context);
    }
}
