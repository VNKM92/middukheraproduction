<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'content',
        'banner_image',
        'sections',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
    ];

    protected $casts = [
        'sections' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Helper to get a page by slug with caching or fallback
     */
    public static function getBySlug(string $slug)
    {
        return static::where('slug', $slug)->first();
    }
}
