<?php

namespace App\Http\Controllers;

use App\Services\KindeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected KindeService $kindeService;

    public function __construct(KindeService $kindeService)
    {
        $this->kindeService = $kindeService;
    }

    /**
     * Show the home page - login if unauthenticated, dashboard if authenticated
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
     */
    public function login(): RedirectResponse
    {
        $loginUrl = $this->kindeService->getLoginUrl();
        return redirect($loginUrl);
    }

    /**
     * Redirect to Kinde registration page
     */
    public function register(): RedirectResponse
    {
        $registerUrl = $this->kindeService->getRegisterUrl();
        return redirect($registerUrl);
    }

    /**
     * Handle the OAuth callback from Kinde
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
     */
    public function logout(): void
    {
        $this->kindeService->logout();
    }

    /**
     * Show the dashboard (protected route)
     */
    public function dashboard(): View
    {
        return view('dashboard');
    }
} 