<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $domain = 'pct.social.br';

        $systemBoxes = [
            ['name' => 'Contato PCT', 'local_part' => 'contato'],
            ['name' => 'Suporte PCT', 'local_part' => 'suporte'],
            ['name' => 'Financeiro PCT', 'local_part' => 'financeiro'],
        ];

        foreach ($systemBoxes as $box) {
            DB::table('directory_mailboxes')->updateOrInsert(
                ['email' => $box['local_part'].'@'.$domain],
                [
                    'directory_id' => null,
                    'name' => $box['name'],
                    'local_part' => $box['local_part'],
                    'domain' => $domain,
                    'is_default' => false,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('directory_mailboxes')
            ->whereIn('email', [
                'contato@pct.social.br',
                'suporte@pct.social.br',
                'financeiro@pct.social.br',
            ])->delete();
    }
};
