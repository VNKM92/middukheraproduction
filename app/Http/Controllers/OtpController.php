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
        ]);

        if (empty($request->phone) && empty($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid phone number or email address.',
            ], 422);
        }

        $result = OtpService::generateAndSend(
            phone: $request->phone,
            email: $request->email,
            name: $request->name,
            action: $request->action ?? 'booking_verification'
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
