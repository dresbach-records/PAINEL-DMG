<?php

namespace App\Http\Controllers;

use App\Models\DirectoryMailbox;
use App\Services\MailboxAddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailboxController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DirectoryMailbox::query()->latest();

        if ($request->filled('directory_id')) {
            $query->where('directory_id', $request->integer('directory_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    public function store(Request $request, MailboxAddressService $service): JsonResponse
    {
        $data = $request->validate([
            'directory_id' => ['nullable', 'integer'],
            'directory_name' => ['nullable', 'string', 'max:120'],
            'name' => ['nullable', 'string', 'max:120'],
            'local_part' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9._-]+$/i'],
            'is_default' => ['nullable', 'boolean'],
            'forward_to' => ['nullable', 'array'],
        ]);

        if (!empty($data['directory_id']) && !empty($data['directory_name']) && empty($data['local_part'])) {
            $mailbox = $service->createDefaultForDirectory((int) $data['directory_id'], $data['directory_name']);

            return response()->json($mailbox, 201);
        }

        $domain = config('pct-mail.mailbox_domain', 'pct.social.br');
        $localPart = $data['local_part'];

        $mailbox = DirectoryMailbox::query()->create([
            'directory_id' => $data['directory_id'] ?? null,
            'name' => $data['name'] ?? "Caixa {$localPart}",
            'local_part' => $localPart,
            'domain' => $domain,
            'email' => "{$localPart}@{$domain}",
            'is_default' => (bool) ($data['is_default'] ?? false),
            'is_active' => true,
            'forward_to' => $data['forward_to'] ?? [],
        ]);

        return response()->json($mailbox, 201);
    }

    public function show(DirectoryMailbox $mailbox): JsonResponse
    {
        return response()->json($mailbox->load('inboundMessages'));
    }

    public function update(Request $request, DirectoryMailbox $mailbox): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'forward_to' => ['sometimes', 'array'],
        ]);

        $mailbox->update($data);

        return response()->json($mailbox->refresh());
    }
}
