<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'payment/xendit/webhook',
    ];

    /**
     * Handle an incoming request.
     * 
     * Override untuk memberikan error message yang lebih informatif dan handle multiple tabs
     */
    public function handle($request, Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->inExceptArray($request) ||
            $this->tokensMatch($request)
        ) {
            return tap($next($request), function ($response) use ($request) {
                if ($this->shouldAddXsrfTokenCookie()) {
                    $this->addCookieToResponse($request, $response);
                }
            });
        }

        // Jika CSRF token tidak match, log untuk debugging
        Log::warning('CSRF token mismatch', [
            'url' => $request->url(),
            'method' => $request->method(),
            'session_token' => $request->session()->token() ? substr($request->session()->token(), 0, 10) . '...' : 'missing',
            'request_token' => $request->input('_token') ? substr($request->input('_token'), 0, 10) . '...' : 'missing',
            'has_session' => $request->hasSession(),
            'ip' => $request->ip(),
        ]);

        throw new TokenMismatchException('CSRF token mismatch. Silakan refresh halaman dan coba lagi.');
    }
}

