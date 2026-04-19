<?php

namespace App\Http\Controllers;

use App\Models\DirectoryMailbox;
use App\Models\InboundMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InboundMailController extends Controller
{
    public function receive(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'to_email' => ['required', 'email'],
            'from_email' => ['required', 'email'],
            'subject' => ['nullable', 'string', 'max:190'],
            'text_body' => ['nullable', 'string'],
            'html_body' => ['nullable', 'string'],
            'message_id' => ['nullable', 'string', 'max:190'],
            'headers' => ['nullable', 'array'],
            'attachments' => ['nullable', 'array'],
        ]);

        $mailbox = DirectoryMailbox::query()
            ->where('email', $payload['to_email'])
            ->where('is_active', true)
            ->first();

        if (!$mailbox) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'Mailbox não existe ou está inativa.',
            ], 404);
        }

        $message = InboundMessage::query()->create([
            'mailbox_id' => $mailbox->id,
            'message_id' => $payload['message_id'] ?? null,
            'from_email' => $payload['from_email'],
            'to_email' => $payload['to_email'],
            'subject' => $payload['subject'] ?? '(sem assunto)',
            'text_body' => $payload['text_body'] ?? null,
            'html_body' => $payload['html_body'] ?? null,
            'headers' => $payload['headers'] ?? [],
            'attachments' => $payload['attachments'] ?? [],
            'received_at' => now(),
        ]);

        return response()->json([
            'status' => 'accepted',
            'mailbox_id' => $mailbox->id,
            'message_id' => $message->id,
        ], 202);
    }

    public function inbox(DirectoryMailbox $mailbox): JsonResponse
    {
        return response()->json(
            $mailbox->inboundMessages()->latest('received_at')->paginate(25)
        );
    }
}
