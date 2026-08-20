<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OtpVerification extends Model
{
    protected $fillable = [
        'phone',
        'email',
        'otp_code',
        'token',
        'action',
        'status',
        'attempts',
        'expires_at',
        'verified_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    /**
     * Create a new OTP entry with 10 minute expiry
     */
    public static function generateOtp(?string $phone, ?string $email, string $action = 'booking_verification', int $expiryMinutes = 10): self
    {
        // Invalidate old active OTPs for the same phone/email
        static::where(function ($query) use ($phone, $email) {
            if ($phone) {
                $query->where('phone', $phone);
            }
            if ($email) {
                $query->orWhere('email', $email);
            }
        })
        ->where('status', 'pending')
        ->update(['status' => 'expired']);

        // Generate 6 digit numeric code
        $code = (string) random_int(100000, 999999);
        $token = 'otp_tok_' . Str::random(32);

        return static::create([
            'phone' => $phone,
            'email' => $email,
            'otp_code' => $code,
            'token' => $token,
            'action' => $action,
            'status' => 'pending',
            'attempts' => 0,
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Validate entered OTP code
     */
    public function verifyCode(string $code): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if (Carbon::now()->isAfter($this->expires_at)) {
            $this->update(['status' => 'expired']);
            return false;
        }

        if ($this->attempts >= 5) {
            $this->update(['status' => 'expired']);
            return false;
        }

        $this->increment('attempts');

        if (hash_equals(trim($this->otp_code), trim($code))) {
            $this->update([
                'status' => 'verified',
                'verified_at' => Carbon::now(),
            ]);
            return true;
        }

        return false;
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || Carbon::now()->isAfter($this->expires_at);
    }
}
