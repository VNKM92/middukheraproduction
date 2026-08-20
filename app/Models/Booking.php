<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id',
    'package_id',
    'booking_date',
    'status',
    'payment_status',
    'amount',
    'notes',
    'razorpay_order_id',
    'razorpay_payment_id',
    'razorpay_signature'
])]
class Booking extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
