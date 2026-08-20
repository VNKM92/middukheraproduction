<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'category', 'image_path'])]
class Gallery extends Model
{
    protected $table = 'gallery';
}
