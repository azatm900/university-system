<?php

namespace App\Providers;

use App\Facades\Settings;
use App\Services\AuditService;
use App\Services\FileNumberService;
use App\Services\GPAService;
use App\Services\SettingsService;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
        $this->app->singleton(FileNumberService::class);
        $this->app->singleton(GPAService::class);
        $this->app->singleton(AuditService::class);

        // Laravel 11+ removed config/app.php aliases. AliasLoader is the idiomatic way.
        AliasLoader::getInstance()->alias('Settings', Settings::class);
    }

    public function boot(): void
    {
        //
    }
}