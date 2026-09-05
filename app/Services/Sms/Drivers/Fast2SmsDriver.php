<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\Contracts\SmsGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Fast2SmsDriver implements SmsGatewayInterface
{
    protected ?string $apiKey;
    protected string $route;
    protected ?string $senderId;
    protected ?string $entityId;

    public function __construct(
        ?string $apiKey = null,
        string $route = 'q',
        ?string $senderId = null,
        ?string $entityId = null
    ) {
        $this->apiKey = $apiKey ? trim($apiKey) : null;
        $this->route = !empty($route) ? trim($route) : 'q';
        $this->senderId = $senderId ? trim($senderId) : null;
        $this->entityId = $entityId ? trim($entityId) : null;
    }

    public function send(string $to, string $message, array $extra = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'Fast2SMS API key not configured. Please enter your API key in Admin Settings or .env file.',
                'response' => null,
            ];
        }

        // Clean and normalize phone number for Fast2SMS (10-digit Indian Mobile Number)
        $cleanPhone = preg_replace('/[^0-9]/', '', $to);
        if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
            $cleanPhone = substr($cleanPhone, 2);
        } elseif (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        } elseif (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        if (strlen($cleanPhone) !== 10) {
            return [
                'success' => false,
                'message' => "Invalid phone number [{$to}]. Fast2SMS requires a 10-digit Indian mobile number.",
                'response' => null,
            ];
        }

        $activeRoute = $extra['route'] ?? $this->route;

        try {
            $payload = [
                'route' => $activeRoute,
                'message' => $message,
                'language' => 'english',
                'flash' => 0,
                'numbers' => $cleanPhone,
            ];

            if (!empty($this->senderId)) {
                $payload['sender_id'] = $this->senderId;
            }
            if (!empty($this->entityId)) {
                $payload['entity_id'] = $this->entityId;
            }

            $response = Http::withHeaders([
                'authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(15)->post('https://www.fast2sms.com/dev/bulkV2', $payload);

            $json = $response->json();

            // Extract message string safely (Fast2SMS often returns array of messages)
            $responseMsg = '';
            if (isset($json['message'])) {
                $responseMsg = is_array($json['message']) ? implode(' ', $json['message']) : (string)$json['message'];
            }

            $isSuccess = $response->successful() && isset($json['return']) && ($json['return'] === true || $json['return'] === 'true');

            if ($isSuccess) {
                return [
                    'success' => true,
                    'message' => $responseMsg ?: 'SMS dispatched successfully via Fast2SMS.',
                    'response' => $json,
                ];
            }

            // Extract descriptive error
            $errorMessage = $responseMsg;
            if (empty($errorMessage)) {
                $errorMessage = 'Fast2SMS returned HTTP status ' . $response->status() . ': ' . ($response->body() ?: 'Unknown error');
            }

            Log::warning('Fast2SMS Dispatch Failed: ' . $errorMessage, ['response' => $json, 'phone' => $cleanPhone]);

            return [
                'success' => false,
                'message' => 'Fast2SMS Error: ' . $errorMessage,
                'response' => $json ?: ['body' => $response->body(), 'status' => $response->status()],
            ];
        } catch (\Exception $e) {
            Log::error('Fast2SMS Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Fast2SMS Connection Error: ' . $e->getMessage(),
                'response' => null,
            ];
        }
    }
}
