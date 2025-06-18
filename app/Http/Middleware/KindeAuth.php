<?php

namespace App\Http\Middleware;

use App\Services\KindeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KindeAuth
{
    protected KindeService $kindeService;

    public function __construct(KindeService $kindeService)
    {
        $this->kindeService = $kindeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$this->kindeService->isAuthenticated()) {
            // Store the intended URL for redirect after login
            if ($request->getMethod() === 'GET' && !$request->expectsJson()) {
                session(['url.intended' => $request->fullUrl()]);
            }

            // Redirect to login for web requests
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            return redirect()->route('auth.login')
                ->with('error', 'Please log in to access this page.');
        }

        return $next($request);
    }
} 