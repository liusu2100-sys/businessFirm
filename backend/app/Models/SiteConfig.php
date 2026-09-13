<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteConfig extends Model
{
    protected $fillable = ['key', 'value', 'group', 'remark'];

    public static function getValue(string $key, $default = null)
    {
        $all = self::allCached();
        return $all[$key] ?? $default;
    }

    public static function allCached(): array
    {
        return Cache::remember('site_configs', 60, function () {
            return self::query()->pluck('value', 'key')->map(function ($v) {
                $decoded = json_decode($v, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $v;
            })->toArray();
        });
    }

    public static function setValue(string $key, $value, string $group = 'general'): void
    {
        $store = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        self::updateOrCreate(['key' => $key], ['value' => $store, 'group' => $group]);
        Cache::forget('site_configs');
    }
}
