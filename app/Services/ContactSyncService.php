<?php

namespace App\Services;

use App\Models\MailContact;
use Illuminate\Support\Facades\DB;

class ContactSyncService
{
    public function syncFromPct(?int $limit = null): array
    {
        $startedAt = now();

        $stats = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'deactivated' => 0,
        ];

        $connection = config('pct-mail.pct_source.connection', config('database.default'));
        $table = config('pct-mail.pct_source.table', 'pct_people');

        $query = DB::connection($connection)->table($table)
            ->select([
                'id',
                'nome as name',
                'email',
                'telefone as phone',
                'uf as state',
                'cidade as city',
                'directory_id',
                'tipo as type',
                'updated_at',
            ])
            ->whereNotNull('email');

        if ($limit) {
            $query->limit($limit);
        }

        $rows = $query->get();
        $seenKeys = [];

        foreach ($rows as $row) {
            $normalizedType = in_array($row->type, ['afiliado', 'integrante'], true) ? $row->type : 'afiliado';
            $email = strtolower(trim((string) $row->email));

            if (!$email) {
                $stats['skipped']++;
                continue;
            }

            $seenKey = $email.'|'.$normalizedType;
            $seenKeys[] = $seenKey;

            $contact = MailContact::query()->where('email', $email)->where('type', $normalizedType)->first();

            $payload = [
                'external_id' => (string) $row->id,
                'type' => $normalizedType,
                'name' => $row->name ?: 'Sem nome',
                'email' => $email,
                'phone' => $row->phone,
                'state' => $row->state,
                'city' => $row->city,
                'directory_id' => $row->directory_id,
                'is_active' => true,
                'source_updated_at' => $row->updated_at,
                'metadata' => [
                    'source' => 'pct',
                ],
            ];

            if (!$contact) {
                MailContact::query()->create($payload);
                $stats['imported']++;
                continue;
            }

            $contact->update($payload);
            $stats['updated']++;
        }

        $activeContacts = MailContact::query()->whereIn('type', ['afiliado', 'integrante'])->where('is_active', true)->get();

        foreach ($activeContacts as $contact) {
            $key = strtolower($contact->email).'|'.$contact->type;
            if (!in_array($key, $seenKeys, true)) {
                $contact->update(['is_active' => false]);
                $stats['deactivated']++;
            }
        }

        DB::table('mail_contact_sync_logs')->insert([
            'source' => 'pct',
            'imported' => $stats['imported'],
            'updated' => $stats['updated'],
            'skipped' => $stats['skipped'],
            'deactivated' => $stats['deactivated'],
            'status' => 'ok',
            'started_at' => $startedAt,
            'finished_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'context' => json_encode(['rows' => $rows->count()]),
        ]);

        return $stats;
    }
}
