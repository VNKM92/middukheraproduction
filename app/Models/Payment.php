<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['booking_id', 'razorpay_payment_id', 'amount', 'status', 'payment_method', 'raw_payload'])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
