<?php

namespace App\Services;

use Kinde\KindeSDK\KindeClientSDK;
use Kinde\KindeSDK\Configuration;
use Kinde\KindeSDK\Sdk\Enums\GrantType;
use Exception;

/**
 * KindeService - Laravel wrapper for Kinde PHP SDK
 * 
 * This service provides a simplified interface to the Kinde PHP SDK for Laravel applications.
 * It handles the most common authentication operations while providing access to the full
 * SDK when needed for advanced use cases.
 * 
 * Key Features:
 * - User authentication status checking
 * - User profile retrieval
 * - OAuth URL generation (login/register)
 * - Logout handling
 * - Permission checking
 * - Direct SDK access for advanced operations
 * 
 * 
 * @package App\Services
 */
class KindeService
{
    /**
     * The Kinde SDK client instance
     */
    protected KindeClientSDK $kindeClient;
    
    /**
     * The Kinde SDK configuration instance
     */
    protected Configuration $kindeConfig;

    /**
     * Create a new KindeService instance
     * 
     * Initializes the Kinde SDK with configuration from Laravel's config system.
     * The configuration is read from config/services.php under the 'kinde' key.
     * 
     * @throws Exception If required configuration is missing
     */
    public function __construct()
    {
        $this->kindeConfig = new Configuration();
        $this->kindeConfig->setHost(config('services.kinde.domain'));

        $this->kindeClient = new KindeClientSDK(
            config('services.kinde.domain'),
            config('services.kinde.redirect_url'),
            config('services.kinde.client_id'),
            config('services.kinde.client_secret'),
            GrantType::authorizationCode,
            config('services.kinde.post_logout_redirect_url'),
            'openid profile email offline', // scopes
            [], // additionalParameters
            '' // protocol
        );
    }

    /**
     * Check if user is authenticated
     * 
     * Determines whether the current user has a valid authentication session
     * with Kinde. This checks for the presence and validity of access tokens.
     * 
     * @return bool True if user is authenticated, false otherwise
     */
    public function isAuthenticated(): bool
    {
        return $this->kindeClient->isAuthenticated;
    }

    /**
     * Get the authenticated user profile
     * 
     * Retrieves the profile information for the currently authenticated user.
     * Returns null if the user is not authenticated.
     * 
     * 
     * @return object|null User profile object or null if not authenticated
     */
    public function getUser(): ?object
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        try {
            $userData = $this->kindeClient->getUserDetails();
            
            // Convert array to object for consistent access
            if (is_array($userData)) {
                $userData = (object) $userData;
            }
            
            return $userData;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get login URL
     * 
     * Generates a URL to redirect users to Kinde's hosted login page.
     * After successful authentication, users will be redirected back to
     * the configured callback URL.
     * 
     * @param array $additionalParams Optional additional parameters to include in the login URL
     * @return string The login URL to redirect users to
     */
    public function getLoginUrl(array $additionalParams = []): string
    {
        $result = $this->kindeClient->login($additionalParams);
        return $result->getRedirectUrl();
    }

    /**
     * Get register URL
     * 
     * Generates a URL to redirect users to Kinde's hosted registration page.
     * After successful registration, users will be redirected back to
     * the configured callback URL.
     * 
     * @param array $additionalParams Optional additional parameters to include in the registration URL
     * @return string The registration URL to redirect users to
     */
    public function getRegisterUrl(array $additionalParams = []): string
    {
        $result = $this->kindeClient->register($additionalParams);
        return $result->getRedirectUrl();
    }

    /**
     * Perform logout
     * 
     * Logs the user out of both the application and Kinde.
     * This method will redirect to Kinde's logout endpoint, which will
     * clear the user's session and then redirect to the configured
     * post-logout URL.
     * 
     * Note: This method performs a redirect and does not return.
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->kindeClient->logout();
    }

    /**
     * Handle the OAuth callback
     * 
     * Processes the OAuth2 authorization code received from Kinde's callback
     * and exchanges it for access and ID tokens. This method should be called
     * in your callback route handler.
     * 
     * @return bool True if the callback was handled successfully and user is now authenticated
     */
    public function handleCallback(): bool
    {
        try {
            $this->kindeClient->getToken();
            return $this->isAuthenticated();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if user has a specific permission
     * 
     * Determines whether the currently authenticated user has been granted
     * a specific permission. Returns false if the user is not authenticated
     * or if the permission check fails.
     * 
     * 
     * @param string $permission The permission to check (e.g., 'create:posts')
     * @return bool True if user has the permission, false otherwise
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        try {
            $result = $this->kindeClient->getPermission($permission);
            return $result['isGranted'] ?? false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get the configured Kinde client for direct SDK access
     * 
     * Returns the underlying Kinde PHP SDK client for advanced operations
     * not covered by this service. The client is already configured
     * with your application settings.
     * 
     * Use this when you need functionality listed in the Kinde PHP SDK documentation.
     * https://docs.kinde.com/developer-tools/sdks/backend/php-sdk/#kindesdk-methods
     * 
     * 
     * @return KindeClientSDK The configured Kinde PHP SDK client
     */
    public function client(): KindeClientSDK
    {
        return $this->kindeClient;
    }
} 