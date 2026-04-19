<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailbox_id',
        'message_id',
        'from_email',
        'to_email',
        'subject',
        'text_body',
        'html_body',
        'headers',
        'attachments',
        'received_at',
    ];

    protected $casts = [
        'headers' => 'array',
        'attachments' => 'array',
        'received_at' => 'datetime',
    ];

    public function mailbox()
    {
        return $this->belongsTo(DirectoryMailbox::class, 'mailbox_id');
    }
}
