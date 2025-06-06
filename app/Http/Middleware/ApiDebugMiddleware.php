<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Log;

class ApiDebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log incoming API requests
        Log::info('DEBUG API REQUEST: ' . $request->fullUrl(), [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'params' => $request->all()
        ]);
        
        // Get the response
        $response = $next($request);
        
        // Only log JSON responses (APIs)
        if ($request->wantsJson() || $request->is('*/api/*') || $request->is('*tunggakan*') || strpos($request->fullUrl(), 'tunggakan') !== false) {
            $content = $response->getContent();
            
            // Try to decode JSON to get a clean log
            try {
                $json = json_decode($content);
                Log::info('DEBUG API RESPONSE: ' . $request->fullUrl(), [
                    'status' => $response->getStatusCode(),
                    'response' => json_decode($content, true)
                ]);
            } catch (\Exception $e) {
                Log::info('DEBUG API RESPONSE (RAW): ' . $request->fullUrl(), [
                    'status' => $response->getStatusCode(),
                    'response' => $content
                ]);
            }
        }
        
        return $response;
    }
}
