@extends('layouts.app')

@section('content')
<div
    x-data="mailDashboard({ mailboxes: @js($mailboxes) })"
    x-init="init()"
    class="p-6 max-w-7xl mx-auto space-y-6"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">RondClub-style Mail Center</h1>
            <p class="text-sm text-gray-600">Compositor completo, logs, inbox e personalização da caixa institucional.</p>
        </div>
        <button @click="composing = true" class="px-4 py-2 rounded bg-blue-600 text-white">Novo e-mail</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-lg border bg-green-50"><p class="text-xs text-gray-600">Enviados</p><p class="text-2xl font-semibold">{{ $stats['sent'] }}</p></div>
        <div class="p-4 rounded-lg border bg-yellow-50"><p class="text-xs text-gray-600">Na fila</p><p class="text-2xl font-semibold">{{ $stats['queued'] }}</p></div>
        <div class="p-4 rounded-lg border bg-red-50"><p class="text-xs text-gray-600">Falhas</p><p class="text-2xl font-semibold">{{ $stats['failed'] }}</p></div>
    </div>

    <div class="flex gap-2 border-b pb-2">
        <button class="px-3 py-1 rounded" :class="tab==='overview' ? 'bg-gray-900 text-white' : 'bg-gray-100'" @click="tab='overview'">Visão geral</button>
        <button class="px-3 py-1 rounded" :class="tab==='logs' ? 'bg-gray-900 text-white' : 'bg-gray-100'" @click="tab='logs'">Logs</button>
        <button class="px-3 py-1 rounded" :class="tab==='inbox' ? 'bg-gray-900 text-white' : 'bg-gray-100'" @click="tab='inbox'; loadInbox()">Inbox</button>
    </div>

    <section x-show="tab==='overview'" class="space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="border rounded p-4 bg-white space-y-3">
                <h2 class="font-semibold">Caixas ativas</h2>
                <select x-model="selectedMailboxId" @change="syncMailboxSettings()" class="w-full border rounded p-2">
                    <template x-for="mailbox in mailboxes" :key="mailbox.id">
                        <option :value="mailbox.id" x-text="`${mailbox.name} <${mailbox.email}>`"></option>
                    </template>
                </select>

                <div class="text-sm text-gray-600">
                    Use esta seção para personalizar remetente, foto da instituição, assinatura e reply-to.
                </div>
            </div>

            <div class="border rounded p-4 bg-white space-y-3">
                <h2 class="font-semibold">Preview do remetente</h2>
                <div class="flex items-center gap-3">
                    <img :src="mailboxSettings.avatar_url || 'https://via.placeholder.com/56?text=PCT'" alt="Logo instituição" class="w-14 h-14 rounded-full border object-cover">
                    <div>
                        <p class="font-medium" x-text="mailboxSettings.display_name || 'Nome do Remetente'"></p>
                        <p class="text-sm text-gray-500" x-text="mailboxSettings.institution_name || 'Instituição'"></p>
                        <p class="text-xs text-gray-400" x-text="mailboxSettings.reply_to || 'reply-to@dominio.com'"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border rounded p-4 bg-white space-y-3">
            <h2 class="font-semibold">Configurações da caixa</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input x-model="mailboxSettings.display_name" class="border rounded p-2" placeholder="Nome exibido do remetente">
                <input x-model="mailboxSettings.reply_to" class="border rounded p-2" placeholder="reply-to@dominio.com">
                <input x-model="mailboxSettings.avatar_url" class="border rounded p-2" placeholder="URL da foto/logo da instituição">
                <input x-model="mailboxSettings.institution_name" class="border rounded p-2" placeholder="Nome da instituição">
            </div>
            <textarea x-model="mailboxSettings.signature_html" class="w-full border rounded p-2 h-24" placeholder="Assinatura institucional (HTML)"></textarea>
            <div class="flex justify-end">
                <button @click="saveMailboxSettings" :disabled="savingMailbox" class="px-4 py-2 rounded bg-emerald-600 text-white">
                    <span x-show="!savingMailbox">Salvar configurações</span>
                    <span x-show="savingMailbox">Salvando...</span>
                </button>
            </div>
        </div>
    </section>

    <section x-show="tab==='logs'" class="overflow-auto">
        <table class="min-w-full text-sm bg-white border rounded">
            <thead class="bg-gray-50"><tr><th class="text-left p-2">Destinatário</th><th class="text-left p-2">Assunto</th><th class="text-left p-2">Status</th><th class="text-left p-2">Data</th></tr></thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr class="border-t"><td class="p-2">{{ $log->recipient }}</td><td class="p-2">{{ $log->subject }}</td><td class="p-2">{{ $log->status }}</td><td class="p-2">{{ optional($log->created_at)->format('d/m/Y H:i') }}</td></tr>
                @empty
                    <tr><td class="p-3 text-gray-500" colspan="4">Sem logs recentes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section x-show="tab==='inbox'" class="space-y-3">
        <div class="flex items-center gap-2">
            <label class="text-sm">Caixa:</label>
            <select x-model="selectedMailboxId" @change="loadInbox(); syncMailboxSettings()" class="border rounded px-2 py-1">
                <template x-for="mailbox in mailboxes" :key="mailbox.id">
                    <option :value="mailbox.id" x-text="mailbox.email"></option>
                </template>
            </select>
        </div>
        <div x-show="loadingInbox" class="text-sm text-gray-500">Carregando mensagens...</div>
        <div class="space-y-2" x-show="!loadingInbox">
            <template x-for="message in inboxMessages" :key="message.id">
                <div class="border rounded p-3 bg-white">
                    <div class="text-sm"><strong>De:</strong> <span x-text="message.from_email"></span></div>
                    <div class="text-sm"><strong>Assunto:</strong> <span x-text="message.subject"></span></div>
                    <div class="text-xs text-gray-500" x-text="formatDate(message.received_at)"></div>
                </div>
            </template>
            <div x-show="!inboxMessages.length" class="text-sm text-gray-500">Sem mensagens nesta caixa.</div>
        </div>
    </section>

    <div x-show="composing" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-lg p-4 space-y-3">
            <div class="flex justify-between items-center"><h3 class="font-semibold">Novo e-mail</h3><button @click="composing=false" class="text-gray-500">✕</button></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <select x-model="form.mailbox_id" class="border rounded p-2">
                    <template x-for="mailbox in mailboxes" :key="mailbox.id"><option :value="mailbox.id" x-text="`Remetente: ${mailbox.email}`"></option></template>
                </select>
                <input x-model="form.to" class="border rounded p-2" placeholder="destino@dominio.com">
                <input x-model="form.subject" class="border rounded p-2" placeholder="Assunto">
                <input x-model="form.template" class="border rounded p-2" placeholder="Template (ex: nao-responder)">
                <select x-model="form.priority" class="border rounded p-2"><option value="low">Baixa</option><option value="normal">Normal</option><option value="high">Alta</option></select>
            </div>
            <textarea x-model="form.data" class="border rounded p-2 w-full h-28" placeholder='{"nome":"João"}'></textarea>
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" x-model="form.queue"> Enviar via fila</label>
            <div class="flex justify-end gap-2"><button @click="composing=false" class="px-3 py-2 rounded border">Cancelar</button><button @click="sendMail" :disabled="sending" class="px-3 py-2 rounded bg-blue-600 text-white"><span x-show="!sending">Enviar</span><span x-show="sending">Enviando...</span></button></div>
        </div>
    </div>
</div>
@vite('resources/js/mail-dashboard.js')
@endsection
