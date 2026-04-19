<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\MailCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(MailCampaign::query()->latest()->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:190'],
            'template_slug' => ['required', 'string', 'max:120'],
            'segment' => ['nullable', 'array'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $campaign = MailCampaign::query()->create($data + [
            'status' => 'draft',
        ]);

        return response()->json($campaign, 201);
    }

    public function show(MailCampaign $campaign): JsonResponse
    {
        return response()->json($campaign);
    }

    public function update(Request $request, MailCampaign $campaign): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'subject' => ['sometimes', 'string', 'max:190'],
            'template_slug' => ['sometimes', 'string', 'max:120'],
            'segment' => ['sometimes', 'array'],
            'scheduled_at' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:draft,scheduled,sending,completed,cancelled'],
        ]);

        $campaign->update($data);

        return response()->json($campaign->refresh());
    }

    public function destroy(MailCampaign $campaign): JsonResponse
    {
        $campaign->delete();

        return response()->json(status: 204);
    }

    public function dispatch(MailCampaign $campaign): JsonResponse
    {
        $campaign->update(['status' => 'sending']);

        foreach ($campaign->resolveRecipients() as $recipient) {
            SendMailJob::dispatch([
                'to' => $recipient['email'],
                'subject' => $campaign->subject,
                'template' => $campaign->template_slug,
                'data' => $recipient['data'] ?? [],
                'priority' => 'normal',
            ])->onQueue('pct-mail');
        }

        return response()->json([
            'status' => 'queued',
            'message' => 'Campanha colocada em fila para envio.',
        ]);
    }
}
