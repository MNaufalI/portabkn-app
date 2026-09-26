<?php

dedclare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param string $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the user is authenticated
        if (! $request->user()) {
            return redirect()->route('login');            
        }

        // Check if the authenticated user has the required role
        if (! $request->user()->hasRole($role)) {
            Log::warning('Unauthorized access attempt', [
                'user_id' => $request->user()->id,
                'attempted_role' => $role,
                'ip_address' => $request->ip(),
            ]);
            
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
