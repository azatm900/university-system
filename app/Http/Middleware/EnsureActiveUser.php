<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $attributes = $user->getAttributes();

        $isActive = true;
        if (array_key_exists('is_active', $attributes)) {
            $raw = $attributes['is_active'];
            $isActive = $raw === null ? true : filter_var($raw, FILTER_VALIDATE_BOOLEAN);
        } elseif (array_key_exists('status', $attributes)) {
            $isActive = in_array($attributes['status'], ['active', 'Active', 'ACTIVE', '1', 1, true], true);
        }

        if (! $isActive) {
            Auth::guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect('/login')
                ->withErrors(['email' => 'حسابك معطّل. يرجى التواصل مع الإدارة.']);
        }

        return $next($request);
    }
}