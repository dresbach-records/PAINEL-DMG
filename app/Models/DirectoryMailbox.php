<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryMailbox extends Model
{
    use HasFactory;

    protected $fillable = [
        'directory_id',
        'name',
        'local_part',
        'domain',
        'email',
        'is_default',
        'is_active',
        'forward_to',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'forward_to' => 'array',
        'metadata' => 'array',
    ];

    public function inboundMessages()
    {
        return $this->hasMany(InboundMessage::class, 'mailbox_id');
    }
}
