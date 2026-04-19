@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 space-y-6">
    <div class="bg-white border rounded-lg p-4 flex items-center gap-4">
        <img src="{{ $iconUrl }}" alt="PCT icon" class="w-12 h-12">
        <div>
            <h1 class="text-2xl font-bold">PCT\Admin\templates-mail</h1>
            <p class="text-sm text-gray-600">Biblioteca oficial de templates de e-mail com identidade do PCT.</p>
        </div>
    </div>

    <div class="bg-white border rounded-lg p-4">
        <img src="{{ $logoUrl }}" alt="PCT logo" class="w-full max-w-xl">
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">Template</th>
                    <th class="p-3 text-left">Slug</th>
                    <th class="p-3 text-left">Assunto</th>
                    <th class="p-3 text-left">View</th>
                    <th class="p-3 text-left">Ativo</th>
                </tr>
            </thead>
            <tbody>
            @forelse($templates as $template)
                <tr class="border-t">
                    <td class="p-3">{{ $template->name }}</td>
                    <td class="p-3">{{ $template->slug }}</td>
                    <td class="p-3">{{ $template->subject }}</td>
                    <td class="p-3">{{ $template->blade_view }}</td>
                    <td class="p-3">{{ $template->is_active ? 'Sim' : 'Não' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-4 text-gray-500">Nenhum template cadastrado.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
