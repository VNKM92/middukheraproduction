<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'recipient',
        'message',
        'driver',
        'template_key',
        'status',
        'response_payload',
        'error_message',
    ];

    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'simulated']);
    }
}
