<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Ensure session is started
        if ($request->hasSession() && !$request->session()->isStarted()) {
            $request->session()->start();
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            // Log for debugging
            \Log::info('RoleMiddleware: User not authenticated', [
                'url' => $request->url(),
                'session_id' => $request->session()->getId(),
                'has_session' => $request->hasSession(),
                'session_started' => $request->hasSession() ? $request->session()->isStarted() : false,
            ]);
            
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Flatten dan explode parameter middleware untuk antisipasi passing string koma maupun array
        $parsedRoles = [];
        foreach ($roles as $roleGroup) {
            $parsedRoles = array_merge($parsedRoles, explode(',', $roleGroup));
        }

        // admin dan admin_pt bersifat ekuivalen
        if ($userRole === 'admin') {
            $userRole = 'admin_pt';
        }
        if (in_array('admin_pt', $parsedRoles) && !in_array('admin', $parsedRoles)) {
            $parsedRoles[] = 'admin';
        }
        if (in_array('admin', $parsedRoles) && !in_array('admin_pt', $parsedRoles)) {
            $parsedRoles[] = 'admin_pt';
        }

        if (!in_array($userRole, $parsedRoles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
