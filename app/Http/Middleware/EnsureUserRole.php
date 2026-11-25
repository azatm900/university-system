<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Usage in routes:  ->middleware('role:admin,registrar')
     * Passing no roles disables the check (any authenticated user passes).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/login');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $role = $user->role;
        $roleValue = $role instanceof \BackedEnum ? $role->value : (string) $role;

        if (in_array($roleValue, $roles, true)) {
            return $next($request);
        }

        abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
    }
}