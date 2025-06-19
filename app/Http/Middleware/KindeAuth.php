<?php

namespace App\Http\Middleware;

use App\Services\KindeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * KindeAuth Middleware
 * 
 * This middleware protects routes by ensuring users are authenticated with Kinde.
 * It provides different behavior for web and API requests:
 * 
 * - Web requests: Redirects to login page and stores intended URL
 * - API requests: Returns 401 JSON response
 * 
 * Usage:
 * - Apply to routes: Route::middleware('kinde.auth')
 * - Apply to controllers: $this->middleware('kinde.auth')
 * - Apply to route groups: Route::middleware('kinde.auth')->group(...)
 * 
 * The middleware is automatically registered as 'kinde.auth' in KindeServiceProvider.
 * 
 * @package App\Http\Middleware
 */
class KindeAuth
{
    /**
     * The Kinde service instance for authentication checks
     */
    protected KindeService $kindeService;

    /**
     * Create a new KindeAuth middleware instance
     * 
     * @param KindeService $kindeService The Kinde service for authentication operations
     */
    public function __construct(KindeService $kindeService)
    {
        $this->kindeService = $kindeService;
    }

    /**
     * Handle an incoming request
     * 
     * This method checks if the user is authenticated and handles different
     * scenarios based on the request type:
     * 
     * 1. If authenticated: Allow request to continue
     * 2. If not authenticated (web request): 
     *    - Store intended URL for post-login redirect
     *    - Redirect to login page with error message
     * 3. If not authenticated (API request):
     *    - Return 401 JSON response
     * 
     * The intended URL is stored in the session and will be used by the
     * authentication flow to redirect users back to their original destination
     * after successful login.
     *
     * @param Request $request The incoming HTTP request
     * @param Closure $next The next middleware in the pipeline
     * @return Response The HTTP response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$this->kindeService->isAuthenticated()) {
            // Store the intended URL for redirect after login
            if ($request->getMethod() === 'GET' && !$request->expectsJson()) {
                session(['url.intended' => $request->fullUrl()]);
            }

            // Return JSON response for API requests
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            return redirect()->route('auth.login')
                ->with('error', 'Please log in to access this page.');
        }

        return $next($request);
    }
} 