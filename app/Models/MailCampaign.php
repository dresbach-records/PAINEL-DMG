<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'template_slug',
        'segment',
        'status',
        'scheduled_at',
        'last_dispatched_at',
    ];

    protected $casts = [
        'segment' => 'array',
        'scheduled_at' => 'datetime',
        'last_dispatched_at' => 'datetime',
    ];

    /**
     * Stub para integração com filtros do SaaS.
     * Troque por queries reais de filiados/diretórios.
     */
    public function resolveRecipients(): array
    {
        return $this->segment['recipients'] ?? [];
    }
}
