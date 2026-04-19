<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\DirectoryMailbox;
use App\Models\MailLog;
use App\Services\PCTMailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function dashboard(): \Illuminate\View\View
    {
        return view('mail-dashboard', [
            'stats' => [
                'sent' => MailLog::query()->where('status', 'sent')->count(),
                'failed' => MailLog::query()->where('status', 'failed')->count(),
                'queued' => MailLog::query()->where('status', 'queued')->count(),
            ],
            'recentLogs' => MailLog::query()->latest()->limit(10)->get(),
            'mailboxes' => DirectoryMailbox::query()
                ->where('is_active', true)
                ->orderBy('email')
                ->limit(100)
                ->get(['id', 'name', 'email', 'metadata', 'forward_to']),
        ]);
    }

    public function send(Request $request, PCTMailer $mailer): JsonResponse
    {
        $payload = $request->validate([
            'to' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:190'],
            'template' => ['required', 'string'],
            'data' => ['nullable', 'array'],
            'priority' => ['nullable', 'in:low,normal,high'],
            'queue' => ['nullable', 'boolean'],
            'mailbox_id' => ['nullable', 'integer', 'exists:directory_mailboxes,id'],
        ]);

        $mailbox = null;
        $from = null;
        $replyTo = null;

        if (!empty($payload['mailbox_id'])) {
            $mailbox = DirectoryMailbox::query()->where('is_active', true)->find($payload['mailbox_id']);

            if ($mailbox) {
                $profile = $mailbox->metadata['sender_profile'] ?? [];

                $from = [
                    'address' => $mailbox->email,
                    'name' => $profile['display_name'] ?? $mailbox->name,
                ];

                $replyTo = $profile['reply_to'] ?? null;
            }
        }

        $shouldQueue = $payload['queue'] ?? true;

        $dispatchPayload = $payload + [
            'from' => $from,
            'reply_to' => $replyTo,
        ];

        if ($shouldQueue) {
            SendMailJob::dispatch($dispatchPayload)->onQueue('pct-mail');

            return response()->json([
                'status' => 'queued',
                'message' => 'E-mail adicionado à fila com sucesso.',
            ], 202);
        }

        $log = $mailer->send(
            to: $payload['to'],
            subject: $payload['subject'],
            template: $payload['template'],
            data: $payload['data'] ?? [],
            priority: $payload['priority'] ?? 'normal',
            from: $from,
            replyTo: $replyTo,
        );

        return response()->json([
            'status' => $log->status,
            'message' => $log->error_message ?: 'E-mail enviado com sucesso.',
            'log_id' => $log->id,
        ]);
    }

    public function logs(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 25);

        $logs = MailLog::query()
            ->latest()
            ->paginate($perPage);

        return response()->json($logs);
    }
}
