<?php

namespace App\Http\Controllers;

use App\Models\MailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(MailTemplate::query()->latest()->paginate());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'unique:mail_templates,slug'],
            'subject' => ['required', 'string', 'max:190'],
            'blade_view' => ['required', 'string', 'max:190'],
            'is_active' => ['boolean'],
            'variables' => ['nullable', 'array'],
        ]);

        $template = MailTemplate::query()->create($data);

        return response()->json($template, 201);
    }

    public function show(MailTemplate $template): JsonResponse
    {
        return response()->json($template);
    }

    public function update(Request $request, MailTemplate $template): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'subject' => ['sometimes', 'string', 'max:190'],
            'blade_view' => ['sometimes', 'string', 'max:190'],
            'is_active' => ['sometimes', 'boolean'],
            'variables' => ['sometimes', 'array'],
        ]);

        $template->update($data);

        return response()->json($template->refresh());
    }

    public function destroy(MailTemplate $template): JsonResponse
    {
        $template->delete();

        return response()->json(status: 204);
    }
}
