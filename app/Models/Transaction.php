<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_ref',
        'booking_id',
        'user_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'customer_name',
        'customer_email',
        'customer_phone',
        'failure_reason',
        'ip_address',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'raw_response' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCaptured(): bool
    {
        return $this->status === 'captured';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['initiated', 'pending_otp', 'otp_verified', 'processing']);
    }

    /**
     * Helper for status badge colors in UI
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'captured' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'border' => 'border-emerald-500/20', 'label' => 'Captured'],
            'initiated' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'border' => 'border-amber-500/20', 'label' => 'Initiated'],
            'pending_otp' => ['bg' => 'bg-cyan-500/10', 'text' => 'text-cyan-400', 'border' => 'border-cyan-500/20', 'label' => 'Pending OTP'],
            'otp_verified' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'border' => 'border-blue-500/20', 'label' => 'OTP Verified'],
            'processing' => ['bg' => 'bg-indigo-500/10', 'text' => 'text-indigo-400', 'border' => 'border-indigo-500/20', 'label' => 'Processing'],
            'failed' => ['bg' => 'bg-rose-500/10', 'text' => 'text-rose-400', 'border' => 'border-rose-500/20', 'label' => 'Failed'],
            'refunded' => ['bg' => 'bg-purple-500/10', 'text' => 'text-purple-400', 'border' => 'border-purple-500/20', 'label' => 'Refunded'],
            default => ['bg' => 'bg-zinc-500/10', 'text' => 'text-zinc-400', 'border' => 'border-zinc-500/20', 'label' => ucfirst($this->status)],
        };
    }
}
