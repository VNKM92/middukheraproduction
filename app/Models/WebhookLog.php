<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'event_id',
        'event_type',
        'signature',
        'is_valid_signature',
        'processed',
        'status_message',
        'payload',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'is_valid_signature' => 'boolean',
            'processed' => 'boolean',
            'payload' => 'array',
        ];
    }
}
