<?php

namespace App\Services\Payment;

use App\Models\Setting;

class PaymentGatewayManager
{
    /**
     * Get the active default payment gateway key ('cashfree' or 'razorpay')
     */
    public static function getActiveGateway(): string
    {
        // 1. Check if admin explicitly enabled/set Cashfree or Razorpay
        $activeSetting = Setting::get('active_payment_gateway');
        if (!empty($activeSetting)) {
            $activeSetting = strtolower(trim($activeSetting));
            if (in_array($activeSetting, ['cashfree', 'razorpay'], true)) {
                return $activeSetting;
            }
        }

        // 2. Check individual enable toggles
        $cashfreeEnabled = Setting::get('cashfree_enabled', '1');
        $razorpayEnabled = Setting::get('razorpay_enabled', '1');

        if ($cashfreeEnabled == '1' && $razorpayEnabled != '1') {
            return 'cashfree';
        }

        if ($razorpayEnabled == '1' && $cashfreeEnabled != '1') {
            return 'razorpay';
        }

        // Default to cashfree when configured, otherwise razorpay
        $cashfreeKey = Setting::get('cashfree_app_id');
        if (!empty($cashfreeKey)) {
            return 'cashfree';
        }

        return 'cashfree';
    }

    /**
     * Check if Cashfree gateway is enabled
     */
    public static function isCashfreeEnabled(): bool
    {
        $enabled = Setting::get('cashfree_enabled', '1');
        return $enabled == '1' || $enabled === true;
    }

    /**
     * Check if Razorpay gateway is enabled
     */
    public static function isRazorpayEnabled(): bool
    {
        $enabled = Setting::get('razorpay_enabled', '1');
        return $enabled == '1' || $enabled === true;
    }

    /**
     * Get Cashfree Service instance
     */
    public static function cashfree(): CashfreeService
    {
        return app(CashfreeService::class);
    }

    /**
     * Get Razorpay Service instance
     */
    public static function razorpay(): RazorpayService
    {
        return app(RazorpayService::class);
    }
}
