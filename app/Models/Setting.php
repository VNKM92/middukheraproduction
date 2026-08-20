<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
    ];

    /**
     * Get a setting value by key with a fallback default.
     */
    public static function get(string $key, $default = null)
    {
        $settings = static::getAllAsArray();
        return $settings[$key] ?? $default;
    }

    /**
     * Set or update a setting.
     */
    public static function set(string $key, $value, string $group = 'general', string $type = 'text', ?string $label = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'label' => $label ?? ucwords(str_replace('_', ' ', $key)),
            ]
        );

        Cache::forget('site_settings_all');
        return $setting;
    }

    /**
     * Get all settings cached as an associative array.
     */
    public static function getAllAsArray(): array
    {
        return Cache::rememberForever('site_settings_all', function () {
            try {
                return static::pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings_all');
    }
}
