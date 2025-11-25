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

        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
