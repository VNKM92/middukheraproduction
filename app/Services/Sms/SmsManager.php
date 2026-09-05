<?php

namespace App\Services\Sms;

use App\Models\Setting;
use App\Models\SmsLog;
use App\Services\Sms\Contracts\SmsGatewayInterface;
use App\Services\Sms\Drivers\AutoFailoverDriver;
use App\Services\Sms\Drivers\CustomHttpDriver;
use App\Services\Sms\Drivers\Fast2SmsDriver;
use App\Services\Sms\Drivers\LogDriver;
use App\Services\Sms\Drivers\Msg91Driver;
use App\Services\Sms\Drivers\TwilioDriver;

class SmsManager
{
    /**
     * Default template messages with placeholders
     */
    public const DEFAULT_TEMPLATES = [
        'otp' => "{otp} is OTP for online purchase of Rs. {amount} at {merchant} thru {card_name} and last 4 digit number  like {card_last4}. Do not share this OTP with anyone. - {card_name}",
        'payment_success' => "Spent Rs.{amount} From {bank_card} At {merchant} On {datetime} Bal Rs.{balance} Not You? Call 18002586161/SMS BLOCK DC  {card_last4} to 7308080808",
        'payment_failed' => "Dear {name}, your payment of {currency}{amount} for booking #{booking_id} could not be completed. Reason: {reason}. Please retry at: {retry_url}",
        'admin_alert' => "[ALERT] New booking #{booking_id} confirmed by {name} for {package}. Amount: {currency}{amount}.",
    ];

    /**
     * Resolve setting with fallback from database -> config -> env
     */
    public static function resolveConfig(string $settingKey, ?string $configKey = null, ?string $envKey = null, $default = null): ?string
    {
        // 1. Check Database Settings Table
        $dbVal = Setting::get($settingKey);
        if ($dbVal !== null && trim((string)$dbVal) !== '') {
            return trim((string)$dbVal);
        }

        // 2. Check Laravel Config
        if ($configKey) {
            $configVal = config($configKey);
            if ($configVal !== null && trim((string)$configVal) !== '') {
                return trim((string)$configVal);
            }
        }

        // 3. Check Direct Environment Variable
        if ($envKey) {
            $envVal = env($envKey);
            if ($envVal !== null && trim((string)$envVal) !== '') {
                return trim((string)$envVal);
            }
        }

        return $default;
    }

    /**
     * Get instance of configured SMS driver with optional override
     */
    public static function getDriver(?string $overrideDriver = null): SmsGatewayInterface
    {
        $smsEnabled = self::resolveConfig('sms_enabled', 'services.sms_enabled', 'SMS_ENABLED', '1');

        if ($overrideDriver !== 'simulation' && in_array($smsEnabled, ['0', 'false', false], true)) {
            return new LogDriver();
        }

        $driverName = $overrideDriver ?: self::resolveConfig('sms_driver', 'services.sms_driver', 'SMS_DRIVER', 'auto');

        // Resolve Twilio Credentials
        $twilioSid = self::resolveConfig('twilio_sid', 'services.twilio.sid', 'TWILIO_ACCOUNT_SID') ?: env('TWILIO_SID');
        $twilioToken = self::resolveConfig('twilio_token', 'services.twilio.token', 'TWILIO_AUTH_TOKEN') ?: env('TWILIO_TOKEN');
        $twilioFrom = self::resolveConfig('twilio_from_number', 'services.twilio.from', 'TWILIO_FROM_NUMBER') ?: env('TWILIO_FROM');

        $twilioDriver = new TwilioDriver(
            sid: $twilioSid,
            token: $twilioToken,
            fromNumber: $twilioFrom
        );

        // Resolve Fast2SMS Credentials
        $fast2smsKey = self::resolveConfig('fast2sms_api_key', 'services.fast2sms.api_key', 'FAST2SMS_API_KEY');
        $fast2smsRoute = self::resolveConfig('fast2sms_route', 'services.fast2sms.route', 'FAST2SMS_ROUTE', 'q');
        $fast2smsSenderId = self::resolveConfig('fast2sms_sender_id', 'services.fast2sms.sender_id', 'FAST2SMS_SENDER_ID');
        $fast2smsEntityId = self::resolveConfig('fast2sms_entity_id', 'services.fast2sms.entity_id', 'FAST2SMS_ENTITY_ID');

        $fast2smsDriver = new Fast2SmsDriver(
            apiKey: $fast2smsKey,
            route: $fast2smsRoute,
            senderId: $fast2smsSenderId,
            entityId: $fast2smsEntityId
        );

        return match ($driverName) {
            'auto', 'twilio_fast2sms' => new AutoFailoverDriver(
                twilio: $twilioDriver,
                fast2sms: $fast2smsDriver
            ),
            'twilio' => $twilioDriver,
            'fast2sms' => $fast2smsDriver,
            'msg91' => new Msg91Driver(
                authKey: self::resolveConfig('msg91_auth_key', null, 'MSG91_AUTH_KEY'),
                senderId: self::resolveConfig('msg91_sender_id', null, 'MSG91_SENDER_ID', 'MIDDUK'),
                dltTemplateId: self::resolveConfig('msg91_dlt_template_id', null, 'MSG91_DLT_TEMPLATE_ID')
            ),
            'custom_http' => new CustomHttpDriver(
                url: self::resolveConfig('custom_sms_url', null, 'CUSTOM_SMS_URL'),
                method: self::resolveConfig('custom_sms_method', null, 'CUSTOM_SMS_METHOD', 'GET'),
                headersJson: self::resolveConfig('custom_sms_headers', null, 'CUSTOM_SMS_HEADERS')
            ),
            default => new LogDriver(),
        };
    }

    /**
     * Parse message template replacing all tags
     */
    public static function parseTemplate(string $templateKey, array $data): string
    {
        $template = Setting::get("sms_template_{$templateKey}") ?: (self::DEFAULT_TEMPLATES[$templateKey] ?? '');
        $siteName = Setting::get('site_name', 'Middukhera Production');
        $currency = Setting::get('currency_symbol', 'Rs.');

        $placeholders = array_merge([
            '{site_name}' => $siteName,
            '{currency}' => $currency,
            '{merchant}' => $siteName,
            '{bank_card}' => 'HDFC Bank Card x8102',
            '{card_name}' => 'Card Name',
            '{card_last4}' => '7317',
            '{balance}' => '281137.42',
            '{datetime}' => now()->format('Y-m-d:H:i:s'),
        ], array_combine(
            array_map(fn($k) => '{' . $k . '}', array_keys($data)),
            array_values($data)
        ));

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Send OTP SMS to customer
     */
    public static function sendOtpSms(string $phone, string $otp, ?string $name = 'Valued Client', array $extraData = []): array
    {
        $message = self::parseTemplate('otp', array_merge([
            'otp' => $otp,
            'name' => $name ?: 'Client',
            'amount' => '10.00',
            'merchant' => Setting::get('site_name', 'Middukhera Production'),
            'card_name' => 'Card Name',
            'card_last4' => '7317',
        ], $extraData));

        return self::dispatch($phone, $message, 'otp', ['otp' => $otp]);
    }

    /**
     * Send Payment Success SMS
     */
    public static function sendPaymentSuccessSms(string $phone, array $data): array
    {
        $amountFormatted = isset($data['amount']) ? (is_numeric($data['amount']) ? number_format((float)$data['amount'], 0, '.', '') : $data['amount']) : '98890';

        $message = self::parseTemplate('payment_success', array_merge([
            'name' => $data['name'] ?? 'Client',
            'amount' => $amountFormatted,
            'booking_id' => $data['booking_id'] ?? '',
            'package' => $data['package'] ?? 'Photoshoot',
            'payment_id' => $data['payment_id'] ?? '',
            'bank_card' => $data['bank_card'] ?? 'HDFC Bank Card x8102',
            'merchant' => Setting::get('site_name', 'Middukhera Production'),
            'card_last4' => $data['card_last4'] ?? '8102',
            'datetime' => $data['datetime'] ?? now()->format('Y-m-d:H:i:s'),
            'balance' => $data['balance'] ?? '281137.42',
        ], $data));

        return self::dispatch($phone, $message, 'payment_success', $data);
    }

    /**
     * Send Payment Failure SMS
     */
    public static function sendPaymentFailedSms(string $phone, array $data): array
    {
        $message = self::parseTemplate('payment_failed', [
            'name' => $data['name'] ?? 'Client',
            'amount' => number_format((float)($data['amount'] ?? 0)),
            'booking_id' => $data['booking_id'] ?? '',
            'reason' => $data['reason'] ?? 'Payment authorization failed',
            'retry_url' => $data['retry_url'] ?? url('/'),
        ]);

        return self::dispatch($phone, $message, 'payment_failed', $data);
    }

    /**
     * Send Admin Alert SMS
     */
    public static function sendAdminAlertSms(array $data): ?array
    {
        $adminPhone = Setting::get('sms_admin_phone');
        if (empty($adminPhone)) {
            return null;
        }

        $message = self::parseTemplate('admin_alert', [
            'name' => $data['name'] ?? 'Client',
            'amount' => number_format((float)($data['amount'] ?? 0)),
            'booking_id' => $data['booking_id'] ?? '',
            'package' => $data['package'] ?? 'Photoshoot',
        ]);

        return self::dispatch($adminPhone, $message, 'admin_alert', $data);
    }

    /**
     * Core dispatch and logging method
     */
    public static function dispatch(string $phone, string $message, ?string $templateKey = null, array $extra = [], ?string $overrideDriver = null): array
    {
        if (empty(trim($phone))) {
            return ['success' => false, 'message' => 'Phone number cannot be empty.'];
        }

        $driver = self::getDriver($overrideDriver);
        $configuredDriverName = $overrideDriver ?: self::resolveConfig('sms_driver', 'services.sms_driver', 'SMS_DRIVER', 'auto');
        $smsEnabled = self::resolveConfig('sms_enabled', 'services.sms_enabled', 'SMS_ENABLED', '1');

        if ($configuredDriverName !== 'simulation' && in_array($smsEnabled, ['0', 'false', false], true)) {
            $configuredDriverName = 'simulation (disabled)';
        }

        $result = $driver->send($phone, $message, $extra);
        $driverUsed = $result['driver_used'] ?? $configuredDriverName;

        // Record in database SMS logs
        try {
            SmsLog::create([
                'recipient' => $phone,
                'message' => $message,
                'driver' => $driverUsed,
                'template_key' => $templateKey,
                'status' => $result['success'] ? ($driverUsed === 'simulation' ? 'simulated' : 'sent') : 'failed',
                'response_payload' => is_array($result['response'] ?? null) ? json_encode($result['response']) : ($result['response'] ?? null),
                'error_message' => $result['success'] ? null : ($result['message'] ?? 'Dispatch failed'),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to write SmsLog: ' . $e->getMessage());
        }

        return $result;
    }
}
