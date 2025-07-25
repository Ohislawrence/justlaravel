<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
    'value',
    'type', // Add type for casting
        'group' // For organizing settings
    ];

    protected $casts = [
        'value' => 'json' // Store complex values as JSON
    ];

    // Cache settings to reduce database queries
    protected static $cache = [];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        if (!array_key_exists($key, static::$cache)) {
            $setting = static::where('key', $key)->first();
            static::$cache[$key] = $setting ? $setting->value : $default;
        }
        return static::$cache[$key];
    }

    /**
     * Set a setting value
     */
    public static function setValue($key, $value)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        static::$cache[$key] = $value;
        return $setting;
    }
    
}
