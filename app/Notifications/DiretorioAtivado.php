<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DiretorioAtivado extends Notification implements ShouldQueue
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
            ->subject('Diretório ativado com sucesso')
            ->view('emails.comprovante', $this->context);
    }
}
