<?php

namespace App\Facades;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool has(string $key)
 * @method static void set(string $key, mixed $value, string $type = 'string', string $group = 'general')
 * @method static void forget(string $key)
 * @method static array all()
 * @method static array group(string $group)
 * @method static void flush()
 *
 * @see \App\Services\SettingsService
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SettingsService::class;
    }
}