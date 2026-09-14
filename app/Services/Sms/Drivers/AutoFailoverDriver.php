<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Log;

class AutoFailoverDriver implements SmsGatewayInterface
{
    /** @var array<string, SmsGatewayInterface> */
    protected array $drivers = [];

    public function __construct(
        array|TwilioDriver|null $driversOrTwilio = null,
        ?Fast2SmsDriver $fast2sms = null,
        ?Msg91Driver $msg91 = null,
        ?CustomHttpDriver $customHttp = null
    ) {
        if (is_array($driversOrTwilio)) {
            $this->drivers = $driversOrTwilio;
        } else {
            if ($driversOrTwilio instanceof TwilioDriver) $this->drivers['twilio'] = $driversOrTwilio;
            if ($fast2sms) $this->drivers['fast2sms'] = $fast2sms;
            if ($msg91) $this->drivers['msg91'] = $msg91;
            if ($customHttp) $this->drivers['custom_http'] = $customHttp;
        }
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        $failures = [];

        foreach ($this->drivers as $driverName => $driver) {
            $result = $driver->send($to, $message, $extra);

            if (!empty($result['success'])) {
                $driverLabel = count($failures) > 0 ? "{$driverName} (fallback)" : $driverName;
                return array_merge($result, [
                    'driver_used' => $driverLabel,
                ]);
            }

            $reason = $result['message'] ?? 'Dispatch failed';
            $failures[$driverName] = $reason;
            Log::info("SMS Gateway [{$driverName}] failed ({$reason}). Attempting next gateway...", [
                'to' => $to,
            ]);
        }

        if (empty($failures)) {
            return [
                'success' => false,
                'message' => 'No active SMS gateways configured.',
                'driver_used' => 'none',
            ];
        }

        $summary = [];
        foreach ($failures as $d => $r) {
            $summary[] = "[{$d}: {$r}]";
        }

        return [
            'success' => false,
            'message' => 'All SMS Gateways Failed: ' . implode(' | ', $summary),
            'driver_used' => 'failover_failed',
        ];
    }
}
