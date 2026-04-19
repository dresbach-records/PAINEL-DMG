<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'type',
        'name',
        'email',
        'phone',
        'state',
        'city',
        'directory_id',
        'is_active',
        'source_updated_at',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'source_updated_at' => 'datetime',
        'metadata' => 'array',
    ];
}
