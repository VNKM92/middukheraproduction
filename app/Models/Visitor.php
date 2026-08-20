<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['ip_address', 'visited_at'])]
class Visitor extends Model
{
    public $timestamps = false;
}
