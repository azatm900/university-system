<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $emailKey = $email !== ''
                ? $email
                : 'missing:' . $request->ip();

            return [
                Limit::perMinute(5)->by('login-email:' . $emailKey),
                Limit::perMinute(30)->by('login-ip:' . $request->ip()),
            ];
        });
    }
}