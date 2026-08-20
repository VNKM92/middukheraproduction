<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'title',
    'slug',
    'excerpt',
    'content',
    'image_path',
    'meta_title',
    'meta_description',
    'meta_keywords'
])]
class Blog extends Model
{
    //
}
