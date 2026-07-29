<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Routes an 'admin' role is allowed to use.
     * Everything else on the site is superadmin-only.
     *
     * @var array<int, string>
     */
    private const ADMIN_ALLOWED_ROUTE_PREFIXES = [
        'dashboard',
        'attendances',
        'cash-advances',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->role === UserRole::SuperAdmin) {
            return $next($request);
        }

        $routeName = $request->route()?->getName() ?? '';

        $isAllowed = collect(self::ADMIN_ALLOWED_ROUTE_PREFIXES)
            ->contains(fn (string $prefix) => $routeName === $prefix || str_starts_with($routeName, "{$prefix}."));

        abort_unless($isAllowed, 403, 'You do not have permission to access this page.');

        return $next($request);
    }
}