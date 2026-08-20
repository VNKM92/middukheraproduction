<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Log;

class LogDriver implements SmsGatewayInterface
{
    public function send(string $to, string $message, array $extra = []): array
    {
        Log::info("[SIMULATED SMS] To: {$to} | Message: {$message}", $extra);

        return [
            'success' => true,
            'message' => 'Simulated SMS logged successfully.',
            'response' => [
                'recipient' => $to,
                'message' => $message,
                'driver' => 'log_simulation',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}
