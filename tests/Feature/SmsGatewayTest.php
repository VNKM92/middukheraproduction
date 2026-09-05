<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\Sms\Drivers\AutoFailoverDriver;
use App\Services\Sms\Drivers\Fast2SmsDriver;
use App\Services\Sms\Drivers\TwilioDriver;
use App\Services\Sms\SmsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_fast2sms_driver_dispatches_with_clean_phone()
    {
        Http::fake([
            'https://www.fast2sms.com/dev/bulkV2' => Http::response([
                'return' => true,
                'request_id' => 'req_12345',
                'message' => ['SMS sent successfully.'],
            ], 200),
        ]);

        $driver = new Fast2SmsDriver(apiKey: 'dummy_api_key', route: 'q');
        $result = $driver->send('+91 98765 43210', 'Hello Test');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('SMS sent successfully', $result['message']);

        Http::assertSent(function ($request) {
            $data = $request->data();
            return $data['numbers'] === '9876543210' &&
                $data['route'] === 'q' &&
                $request->header('authorization')[0] === 'dummy_api_key';
        });
    }

    public function test_fast2sms_driver_handles_error_response_gracefully()
    {
        Http::fake([
            'https://www.fast2sms.com/dev/bulkV2' => Http::response([
                'return' => false,
                'message' => 'Invalid API Key',
            ], 401),
        ]);

        $driver = new Fast2SmsDriver(apiKey: 'invalid_key');
        $result = $driver->send('9876543210', 'Hello Test');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid API Key', $result['message']);
    }

    public function test_twilio_driver_dispatches_successfully()
    {
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC_TEST_SID/Messages.json' => Http::response([
                'sid' => 'SM_TEST_123',
                'status' => 'queued',
            ], 200),
        ]);

        $driver = new TwilioDriver(
            sid: 'AC_TEST_SID',
            token: 'AUTH_TOKEN_TEST',
            fromNumber: '+1234567890'
        );

        $result = $driver->send('9876543210', 'Twilio Test');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('SM_TEST_123', $result['message']);

        Http::assertSent(function ($request) {
            return $request['To'] === '+919876543210' &&
                $request['From'] === '+1234567890' &&
                $request['Body'] === 'Twilio Test';
        });
    }

    public function test_auto_failover_driver_uses_fast2sms_when_twilio_fails()
    {
        // Twilio fails (e.g. unverified recipient on trial)
        Http::fake([
            'https://api.twilio.com/2010-04-01/Accounts/AC_TEST_SID/Messages.json' => Http::response([
                'code' => 21608,
                'message' => 'The number is unverified.',
            ], 400),
            'https://www.fast2sms.com/dev/bulkV2' => Http::response([
                'return' => true,
                'request_id' => 'req_failover_999',
                'message' => ['SMS sent successfully via Fast2SMS.'],
            ], 200),
        ]);

        $twilio = new TwilioDriver('AC_TEST_SID', 'AUTH_TOKEN', '+1234567890');
        $fast2sms = new Fast2SmsDriver('FAST2SMS_KEY');

        $autoDriver = new AutoFailoverDriver($twilio, $fast2sms);
        $result = $autoDriver->send('9876543210', 'Fallback Test');

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Fast2SMS', $result['message']);
        $this->assertStringContainsString('The number is unverified', $result['message']);
    }

    public function test_sms_manager_resolves_credentials_from_db_and_env()
    {
        Setting::set('fast2sms_api_key', 'DB_FAST2SMS_KEY');
        Setting::set('twilio_sid', 'DB_TWILIO_SID');

        $resolvedFast2sms = SmsManager::resolveConfig('fast2sms_api_key', 'services.fast2sms.api_key', 'FAST2SMS_API_KEY');
        $resolvedTwilio = SmsManager::resolveConfig('twilio_sid', 'services.twilio.sid', 'TWILIO_ACCOUNT_SID');

        $this->assertEquals('DB_FAST2SMS_KEY', $resolvedFast2sms);
        $this->assertEquals('DB_TWILIO_SID', $resolvedTwilio);
    }

    public function test_admin_can_send_test_sms_via_admin_route()
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->post(route('admin.sms.test'), [
            'test_phone' => '9876543210',
            'test_message' => 'Admin Test Message',
            'test_driver' => 'simulation',
        ]);

        $response->assertSessionHas('success');
    }
}
