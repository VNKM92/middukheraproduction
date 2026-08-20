<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'slug', 'price_min', 'price_max', 'description', 'features', 'image_path', 'vendor_id'])]
class Package extends Model
{
    protected function casts(): array
    {
        return [
            'features' => 'array',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
