<?php

namespace App\Services;

use App\Models\DirectoryMailbox;
use Illuminate\Support\Str;

class MailboxAddressService
{
    public function createDefaultForDirectory(int $directoryId, string $directoryName): DirectoryMailbox
    {
        $base = Str::slug($directoryName, '.');
        $localPart = $this->nextAvailableLocalPart($base);

        return DirectoryMailbox::query()->create([
            'directory_id' => $directoryId,
            'name' => "Caixa {$directoryName}",
            'local_part' => $localPart,
            'domain' => config('pct-mail.mailbox_domain', 'pct.social.br'),
            'email' => sprintf('%s@%s', $localPart, config('pct-mail.mailbox_domain', 'pct.social.br')),
            'is_default' => true,
            'is_active' => true,
            'metadata' => [
                'provisioned_by' => 'mailbox-service',
            ],
        ]);
    }

    public function createSystemMailbox(string $localPart, string $name): DirectoryMailbox
    {
        $domain = config('pct-mail.mailbox_domain', 'pct.social.br');

        return DirectoryMailbox::query()->firstOrCreate(
            ['email' => "{$localPart}@{$domain}"],
            [
                'directory_id' => null,
                'name' => $name,
                'local_part' => $localPart,
                'domain' => $domain,
                'is_default' => false,
                'is_active' => true,
            ]
        );
    }

    private function nextAvailableLocalPart(string $base): string
    {
        $candidate = $base;
        $counter = 1;

        while (DirectoryMailbox::query()->where('local_part', $candidate)->exists()) {
            $counter++;
            $candidate = "{$base}{$counter}";
        }

        return $candidate;
    }
}
