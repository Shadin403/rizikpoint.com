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
            'https://admin.kachermart.com'
        ];

        $origin = $request->headers->get('Origin');
        if ($origin && in_array($origin, $allowedOrigins, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Credentials: true');
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, X-XSRF-TOKEN, Accept, Origin');
        header('Access-Control-Max-Age: 86400');

        // Short-circuit preflight requests.
        if ($request->isMethod('OPTIONS')) {
            return response('', 204);
        }

        return $next($request);
    }
}
