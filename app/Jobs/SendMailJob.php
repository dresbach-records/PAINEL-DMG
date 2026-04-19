<?php

namespace App\Jobs;

use App\Services\PCTMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly array $payload)
    {
    }

    public function handle(PCTMailer $mailer): void
    {
        $mailer->send(
            to: $this->payload['to'],
            subject: $this->payload['subject'],
            template: $this->payload['template'],
            data: $this->payload['data'] ?? [],
            priority: $this->payload['priority'] ?? 'normal',
codex/task-title-3dv8ut
            from: $this->payload['from'] ?? null,
            replyTo: $this->payload['reply_to'] ?? null,

 main
        );
    }
}
