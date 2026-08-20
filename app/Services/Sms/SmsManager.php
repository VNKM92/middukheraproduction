<?php

namespace App\Services\Sms;

use App\Models\Setting;
use App\Models\SmsLog;
use App\Services\Sms\Contracts\SmsGatewayInterface;
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
        'otp' => "Your {site_name} verification code is: {otp}. Valid for 10 minutes. Please do not share this code.",
        'payment_success' => "Dear {name}, payment of {currency}{amount} for booking #{booking_id} ({package}) was successful! Txn ID: {payment_id}. Thank you - {site_name}.",
        'payment_failed' => "Dear {name}, your payment of {currency}{amount} for booking #{booking_id} could not be completed. Reason: {reason}. Please retry at: {retry_url}",
        'admin_alert' => "[ALERT] New booking #{booking_id} confirmed by {name} for {package}. Amount: {currency}{amount}.",
    ];

    /**
     * Get instance of configured SMS driver
     */
    public static function getDriver(): SmsGatewayInterface
    {
        $driverName = Setting::get('sms_driver', 'simulation');
        $smsEnabled = Setting::get('sms_enabled', '1');

        if ($smsEnabled !== '1') {
            return new LogDriver();
        }

        return match ($driverName) {
            'fast2sms' => new Fast2SmsDriver(
                apiKey: Setting::get('fast2sms_api_key')
            ),
            'msg91' => new Msg91Driver(
                authKey: Setting::get('msg91_auth_key'),
                senderId: Setting::get('msg91_sender_id', 'LUMINA'),
                dltTemplateId: Setting::get('msg91_dlt_template_id')
            ),
            'twilio' => new TwilioDriver(
                sid: Setting::get('twilio_sid'),
                token: Setting::get('twilio_token'),
                fromNumber: Setting::get('twilio_from_number')
            ),
            'custom_http' => new CustomHttpDriver(
                url: Setting::get('custom_sms_url'),
                method: Setting::get('custom_sms_method', 'GET'),
                headersJson: Setting::get('custom_sms_headers')
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
        $siteName = Setting::get('site_name', 'Lumina Studio');
        $currency = Setting::get('currency_symbol', '₹');

        $placeholders = array_merge([
            '{site_name}' => $siteName,
            '{currency}' => $currency,
        ], array_combine(
            array_map(fn($k) => '{' . $k . '}', array_keys($data)),
            array_values($data)
        ));

        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Send OTP SMS to customer
     */
    public static function sendOtpSms(string $phone, string $otp, ?string $name = 'Valued Client'): array
    {
        $message = self::parseTemplate('otp', [
            'otp' => $otp,
            'name' => $name ?: 'Client',
        ]);

        return self::dispatch($phone, $message, 'otp', ['otp' => $otp]);
    }

    /**
     * Send Payment Success SMS
     */
    public static function sendPaymentSuccessSms(string $phone, array $data): array
    {
        $message = self::parseTemplate('payment_success', [
            'name' => $data['name'] ?? 'Client',
            'amount' => number_format((float)($data['amount'] ?? 0)),
            'booking_id' => $data['booking_id'] ?? '',
            'package' => $data['package'] ?? 'Photoshoot',
            'payment_id' => $data['payment_id'] ?? '',
        ]);

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
    public static function dispatch(string $phone, string $message, ?string $templateKey = null, array $extra = []): array
    {
        if (empty(trim($phone))) {
            return ['success' => false, 'message' => 'Phone number cannot be empty.'];
        }

        $driver = self::getDriver();
        $driverName = Setting::get('sms_driver', 'simulation');
        $smsEnabled = Setting::get('sms_enabled', '1');

        if ($smsEnabled !== '1') {
            $driverName = 'simulation (disabled)';
        }

        $result = $driver->send($phone, $message, $extra);

        // Record in database SMS logs
        try {
            SmsLog::create([
                'recipient' => $phone,
                'message' => $message,
                'driver' => $driverName,
                'template_key' => $templateKey,
                'status' => $result['success'] ? ($driverName === 'simulation' ? 'simulated' : 'sent') : 'failed',
                'response_payload' => is_array($result['response'] ?? null) ? json_encode($result['response']) : ($result['response'] ?? null),
                'error_message' => $result['success'] ? null : ($result['message'] ?? 'Dispatch failed'),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to write SmsLog: ' . $e->getMessage());
        }

        return $result;
    }
}
