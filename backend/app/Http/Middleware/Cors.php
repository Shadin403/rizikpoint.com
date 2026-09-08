<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * Adds CORS headers so the storefront frontend (on a different domain)
     * can call the API.  Preflight (OPTIONS) requests are short-circuited
     * with a 204 response.
     */
    public function handle(Request $request, Closure $next)
    {
        // Allow the storefront frontend and the admin panel.  Add more origins
        // here as needed.
        $allowedOrigins = [
            'https://store1.shadin.info',
            'https://store1-admin.shadin.info',
            'http://localhost:5173',
            'http://localhost:3000',
            'http://127.0.0.1:5173',
            'http://127.0.0.1:3000',
            'http://kachermart.com',
            'http://admin.kachermart.com',
            'https://kachermart.com',
            'https://admin.kachermart.com',
            'https://rizikpoint.com',
            'https://www.rizikpoint.com',
            'https://admin.rizikpoint.com',
            'http://rizikpoint.com',
            'http://www.rizikpoint.com',
            'http://admin.rizikpoint.com'
        ];

        $origin = $request->headers->get('Origin');
        $originAllowed = $origin && (
            in_array($origin, $allowedOrigins, true)
            || preg_match('#^https?://([a-z0-9-]+\.)*rizikpoint\.com$#i', $origin)
        );

        // Short-circuit preflight requests, then attach the same headers to
        // both preflight and normal API responses.
        $response = $request->isMethod('OPTIONS')
            ? response('', 204)
            : $next($request);

        if ($originAllowed) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Vary', 'Origin');
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN, Accept, Origin');
        $response->headers->set('Access-Control-Max-Age', '86400');

        return $response;
    }
}
