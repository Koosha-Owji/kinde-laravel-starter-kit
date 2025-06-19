<?php

namespace App\Http\Controllers;

use App\Services\KindeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * AuthController handles all authentication-related routes and flows
 * 
 * This controller manages the complete OAuth2 authentication flow with Kinde:
 * - Redirecting users to login/register
 * - Handling OAuth callbacks
 * - Managing logout
 * - Protecting authenticated routes
 * 
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * The Kinde service instance for handling authentication operations
     */
    protected KindeService $kindeService;

    /**
     * Create a new AuthController instance
     * 
     * @param KindeService $kindeService The Kinde service for authentication operations
     */
    public function __construct(KindeService $kindeService)
    {
        $this->kindeService = $kindeService;
    }

    /**
     * Show the home page - login if unauthenticated, dashboard if authenticated
     * 
     * This method serves as the main entry point for the application.
     * It automatically redirects authenticated users to the dashboard
     * and shows the welcome page to unauthenticated users.
     * 
     * @return View|RedirectResponse Welcome view for guests, redirect to dashboard for authenticated users
     */
    public function index(): View|RedirectResponse
    {
        if ($this->kindeService->isAuthenticated()) {
            return redirect()->route('dashboard');
        }

        return view('welcome');
    }

    /**
     * Redirect to Kinde login page
     * 
     * Generates a login URL with the configured Kinde settings and redirects
     * the user to Kinde's hosted login page. After successful authentication,
     * the user will be redirected back to the configured callback URL.
     * 
     * @return RedirectResponse Redirect to Kinde's login page
     */
    public function login(): RedirectResponse
    {
        $loginUrl = $this->kindeService->getLoginUrl();
        return redirect($loginUrl);
    }

    /**
     * Redirect to Kinde registration page
     * 
     * Generates a registration URL with the configured Kinde settings and redirects
     * the user to Kinde's hosted registration page. After successful registration,
     * the user will be redirected back to the configured callback URL.
     * 
     * @return RedirectResponse Redirect to Kinde's registration page
     */
    public function register(): RedirectResponse
    {
        $registerUrl = $this->kindeService->getRegisterUrl();
        return redirect($registerUrl);
    }

    /**
     * Handle the OAuth callback from Kinde
     * 
     * This method processes the OAuth2 callback after a user has authenticated
     * or registered with Kinde. It handles various scenarios:
     * - Success: Redirects to dashboard with success message
     * - Error: Redirects to home with error message
     * - Missing code: Redirects to home with error message
     * 
     * 
     * @param Request $request The HTTP request containing OAuth callback parameters
     * @return RedirectResponse Redirect to dashboard on success, home on failure
     */
    public function callback(Request $request): RedirectResponse
    {
        // Check for error parameter
        if ($request->has('error')) {
            $error = $request->get('error');
            $errorDescription = $request->get('error_description', 'Authentication failed');
            
            return redirect()->route('home')
                ->with('error', "Authentication error: {$error} - {$errorDescription}");
        }

        // Check for authorization code
        if (!$request->has('code')) {
            return redirect()->route('home')
                ->with('error', 'No authorization code received from Kinde');
        }

        // Handle the callback
        $success = $this->kindeService->handleCallback();

        if ($success) {
            return redirect()->route('dashboard')
                ->with('success', 'Successfully logged in!');
        }

        return redirect()->route('home')
            ->with('error', 'Failed to authenticate with Kinde');
    }

    /**
     * Handle logout
     * 
     * Logs the user out of both the application and Kinde.
     * This method will redirect to Kinde's logout endpoint, which will
     * then redirect back to the configured post-logout URL.
     * 
     * Note: This method does not return as it performs a redirect and exits.
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->kindeService->logout();
    }

    /**
     * Show the dashboard (protected route)
     * 
     * Displays the main dashboard page for authenticated users.
     * This route is protected by the 'kinde.auth' middleware,
     * so only authenticated users can access it.
     * 
     * The dashboard displays user profile information and next steps.
     * 
     * @return View The dashboard view with user data
     */
    public function dashboard(): View
    {
        return view('dashboard');
    }
} 