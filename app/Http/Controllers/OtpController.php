<?php

namespace App\Http\Controllers;

use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    /**
     * Request a new OTP
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'nullable|string|min:7|max:20',
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
            'action' => 'nullable|string|max:50',
            'package_name' => 'nullable|string|max:255',
            'package' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'custom_message' => 'nullable|string|max:500',
            'card_name' => 'nullable|string|max:100',
            'card_last4' => 'nullable|string|max:10',
        ]);

        if (empty($request->phone) && empty($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid phone number or email address.',
            ], 422);
        }

        $extra = [];
        if ($request->filled('amount')) $extra['amount'] = $request->amount;
        if ($request->filled('package_name')) $extra['package_name'] = $request->package_name;
        if ($request->filled('package')) $extra['package'] = $request->package;
        if ($request->filled('custom_message')) $extra['custom_message'] = $request->custom_message;
        if ($request->filled('card_name')) $extra['card_name'] = $request->card_name;
        if ($request->filled('card_last4')) $extra['card_last4'] = $request->card_last4;

        $result = OtpService::generateAndSend(
            phone: $request->phone,
            email: $request->email,
            name: $request->name,
            action: $request->action ?? 'booking_verification',
            extra: $extra
        );

        $status = $result['success'] ? 200 : 429;
        return response()->json($result, $status);
    }

    /**
     * Verify an entered OTP
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'otp' => 'required|string|min:4|max:8',
        ]);

        $result = OtpService::verify($request->token, $request->otp);

        $status = $result['success'] ? 200 : 422;
        return response()->json($result, $status);
    }

    /**
     * Resend an OTP
     */
    public function resend(Request $request): JsonResponse
    {
        return $this->send($request);
    }
}
