@extends('layouts.app')

@section('content')
<div x-data="mailDashboard()" class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">PCT Mail Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-lg bg-green-50 border">Enviados: <strong>{{ $stats['sent'] }}</strong></div>
        <div class="p-4 rounded-lg bg-yellow-50 border">Em fila: <strong>{{ $stats['queued'] }}</strong></div>
        <div class="p-4 rounded-lg bg-red-50 border">Falhas: <strong>{{ $stats['failed'] }}</strong></div>
    </div>

    <div class="p-4 border rounded-lg bg-white space-y-2">
        <h2 class="text-lg font-semibold">Caixas institucionais PCT</h2>
        <ul class="list-disc ml-6 text-sm text-gray-700">
            <li>contato@pct.social.br</li>
            <li>suporte@pct.social.br</li>
            <li>financeiro@pct.social.br</li>
        </ul>
        <p class="text-xs text-gray-500">Cada diretório pode ter uma caixa própria no cadastro.</p>
    </div>

    <button @click="reloadLogs" class="px-4 py-2 bg-blue-600 text-white rounded">Atualizar logs</button>
</div>
@vite('resources/js/mail-dashboard.js')
@endsection
