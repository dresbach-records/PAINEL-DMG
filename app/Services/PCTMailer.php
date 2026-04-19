<?php

namespace App\Services;

use App\Models\MailLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Throwable;

class PCTMailer
{
    public function __construct(private readonly TemplateRenderer $renderer)
    {
    }

    public function send(
        string $to,
        string $subject,
        string $template,
        array $data = [],
        string $priority = 'normal'
    ): MailLog {
        $log = MailLog::query()->create([
            'recipient' => $to,
            'subject' => $subject,
            'template' => $template,
            'status' => 'queued',
            'provider' => config('mail.default', 'smtp'),
            'payload' => $data,
        ]);

        try {
            $html = $this->renderer->render($template, $data);

            $email = (new Email())
                ->to($to)
                ->subject($subject)
                ->html($html)
                ->priority(match ($priority) {
                    'high' => 1,
                    'low' => 5,
                    default => 3,
                });

            Mail::mailer('smtp')->send($email);

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'message_id' => $email->getHeaders()->get('Message-ID')?->getBodyAsString(),
            ]);
        } catch (Throwable $exception) {
            Log::error('PCT Mail send failed', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'error' => $exception->getMessage(),
            ]);

            $log->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }

        return $log->refresh();
    }
}
