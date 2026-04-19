<?php

namespace App\Http\Controllers;

use App\Models\MailContact;
use App\Services\ContactSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MailContact::query()->where('is_active', true);

        if ($request->filled('q')) {
            $term = (string) $request->string('q');
            $query->where(function ($subQuery) use ($term): void {
                $subQuery->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('directory_id')) {
            $query->where('directory_id', $request->integer('directory_id'));
        }

        return response()->json($query->orderBy('name')->paginate($request->integer('per_page', 25)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:2'],
            'city' => ['nullable', 'string', 'max:120'],
            'directory_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'in:manual,afiliado,integrante'],
        ]);

        $contact = MailContact::query()->create($data + [
            'type' => $data['type'] ?? 'manual',
            'is_active' => true,
        ]);

        return response()->json($contact, 201);
    }

    public function update(Request $request, MailContact $contact): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:190'],
            'email' => ['sometimes', 'email', 'max:190'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'state' => ['sometimes', 'nullable', 'string', 'max:2'],
            'city' => ['sometimes', 'nullable', 'string', 'max:120'],
            'directory_id' => ['sometimes', 'nullable', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $contact->update($data);

        return response()->json($contact->refresh());
    }

    public function sync(Request $request, ContactSyncService $service): JsonResponse
    {
        $payload = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ]);

        $result = $service->syncFromPct($payload['limit'] ?? null);

        return response()->json([
            'status' => 'ok',
            'message' => 'Sincronização concluída.',
            'result' => $result,
        ]);
    }
}
