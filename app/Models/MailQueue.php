<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient',
        'subject',
        'template',
        'payload',
        'priority',
        'status',
        'attempts',
        'scheduled_for',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'scheduled_for' => 'datetime',
        'processed_at' => 'datetime',
    ];
}
