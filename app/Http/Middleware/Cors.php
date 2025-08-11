<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Get the origin of the request
        $origin = $request->headers->get('Origin');
        
        // Define allowed origins
        $allowedOrigins = [
            'http://localhost:3000',
            'http://127.0.0.1:3000',
            'https://bfub-git-main-blckjackks-projects.vercel.app',
        ];
        
        // Check if origin is in allowed list or matches vercel pattern
        $isAllowed = in_array($origin, $allowedOrigins) || 
                    (preg_match('/^https:\/\/.*\.vercel\.app$/', $origin));
        
        if ($isAllowed) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        }
        
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(200);
            return $response;
        }

        return $response;
    }
}
