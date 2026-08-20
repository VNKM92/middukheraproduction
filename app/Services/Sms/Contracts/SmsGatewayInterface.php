<?php

namespace App\Services\Sms\Contracts;

interface SmsGatewayInterface
{
    /**
     * Send SMS to a recipient
     *
     * @param string $to Recipient phone number (e.g. +919876543210 or 9876543210)
     * @param string $message Text message content
     * @param array $extra Optional gateway-specific parameters (e.g., template_id, sender_id)
     * @return array ['success' => bool, 'message' => string, 'response' => mixed]
     */
    public function send(string $to, string $message, array $extra = []): array;
}
