<?php

namespace App\Services\Otp;

use App\Models\OtpVerification;
use App\Services\Sms\SmsManager;
use Illuminate\Support\Carbon;

class OtpService
{
    /**
     * Issue an OTP and dispatch via SMS
     */
    public static function generateAndSend(?string $phone, ?string $email = null, ?string $name = null, string $action = 'booking_verification'): array
    {
        if (empty($phone) && empty($email)) {
            return [
                'success' => false,
                'message' => 'A valid phone number or email is required for OTP verification.',
            ];
        }

        // Throttle check: allow only 1 OTP per 60 seconds per phone/email
        $recentOtp = OtpVerification::where(function ($query) use ($phone, $email) {
            if ($phone) {
                $query->where('phone', $phone);
            }
            if ($email) {
                $query->orWhere('email', $email);
            }
        })
        ->where('created_at', '>', Carbon::now()->subSeconds(60))
        ->first();

        if ($recentOtp) {
            $secondsRemaining = 60 - Carbon::now()->diffInSeconds($recentOtp->created_at);
            return [
                'success' => false,
                'message' => "Please wait {$secondsRemaining} seconds before requesting a new verification code.",
                'cooldown' => $secondsRemaining,
                'token' => $recentOtp->token,
            ];
        }

        $otpRecord = OtpVerification::generateOtp($phone, $email, $action, 10);

        // Dispatch Custom SMS if phone is provided
        $smsResult = ['success' => true];
        if ($phone) {
            $smsResult = SmsManager::sendOtpSms($phone, $otpRecord->otp_code, $name);
        }

        return [
            'success' => true,
            'message' => 'Verification code sent successfully to ' . ($phone ? 'your phone' : 'your email') . '.',
            'token' => $otpRecord->token,
            'expires_in' => 600, // 10 mins
            'cooldown' => 60,
            // Only expose otp_code in development / simulation mode for ease of automated testing & debugging
            'simulated_otp' => config('app.debug') || Setting::get('razorpay_simulation_mode', '1') == '1' ? $otpRecord->otp_code : null,
        ];
    }

    /**
     * Verify an entered OTP
     */
    public static function verify(string $token, string $code): array
    {
        $record = OtpVerification::where('token', $token)->first();

        if (!$record) {
            return [
                'success' => false,
                'message' => 'Invalid or expired verification session. Please request a new code.',
            ];
        }

        if ($record->isExpired()) {
            return [
                'success' => false,
                'message' => 'This verification code has expired. Please request a fresh code.',
            ];
        }

        if ($record->status === 'verified') {
            return [
                'success' => true,
                'message' => 'Phone already verified.',
                'verified' => true,
                'token' => $record->token,
            ];
        }

        $isValid = $record->verifyCode($code);

        if ($isValid) {
            return [
                'success' => true,
                'message' => 'Phone verification successful!',
                'verified' => true,
                'token' => $record->token,
            ];
        }

        $attemptsLeft = max(0, 5 - $record->attempts);
        return [
            'success' => false,
            'message' => $attemptsLeft > 0 
                ? "Incorrect verification code. {$attemptsLeft} attempt(s) remaining." 
                : "Too many failed attempts. This code is now invalid. Please request a new one.",
            'attempts_left' => $attemptsLeft,
        ];
    }
}
