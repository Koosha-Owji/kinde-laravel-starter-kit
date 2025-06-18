<?php

namespace App\Services;

use Kinde\KindeSDK\KindeClientSDK;
use Kinde\KindeSDK\Configuration;
use Kinde\KindeSDK\Sdk\Enums\GrantType;
use Kinde\KindeSDK\Sdk\Storage\Storage;
use Exception;

class KindeService
{
    protected KindeClientSDK $kindeClient;
    protected Configuration $kindeConfig;

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
     * Get the Kinde client instance
     */
    public function getClient(): KindeClientSDK
    {
        return $this->kindeClient;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        return $this->kindeClient->isAuthenticated;
    }

    /**
     * Get the authenticated user profile
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
            
            // If picture is missing from user details, try to get it from claims
            if (empty($userData->picture)) {
                $claims = $this->getUserClaims();
                if (isset($claims['picture'])) {
                    $userData->picture = $claims['picture'];
                }
            }
            
            return $userData;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get user permissions
     */
    public function getUserPermissions(): array
    {
        if (!$this->isAuthenticated()) {
            return [];
        }

        try {
            $result = $this->kindeClient->getPermissions();
            // SDK returns: ["orgCode" => "org_1234", "permissions" => ["create:todos", "update:todos"]]
            return $result['permissions'] ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get user claims from ID token
     */
    public function getUserClaims(): array
    {
        if (!$this->isAuthenticated()) {
            return [];
        }

        try {
            $storage = Storage::getInstance();
            $decodedToken = $storage->getDecodedIdToken();
            return $decodedToken ? (array) $decodedToken : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get user organizations
     */
    public function getOrganizations(): array
    {
        if (!$this->isAuthenticated()) {
            return [];
        }

        try {
            $result = $this->kindeClient->getUserOrganizations();
            // SDK returns: ["orgCodes" => ["org_8de8711f46a", "org_820c0f318de"]]
            
            if (isset($result['orgCodes']) && is_array($result['orgCodes'])) {
                $organizations = [];
                foreach ($result['orgCodes'] as $orgCode) {
                    $organizations[] = [
                        'code' => $orgCode,
                        'name' => $orgCode // We only have the code from SDK
                    ];
                }
                return $organizations;
            }
            
            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get login URL
     */
    public function getLoginUrl(array $additionalParams = []): string
    {
        $result = $this->kindeClient->login($additionalParams);
        return $result->getRedirectUrl();
    }

    /**
     * Get register URL
     */
    public function getRegisterUrl(array $additionalParams = []): string
    {
        $result = $this->kindeClient->register($additionalParams);
        return $result->getRedirectUrl();
    }

    /**
     * Perform logout - this will redirect and exit
     */
    public function logout(): void
    {
        $this->kindeClient->logout();
    }

    /**
     * Handle the OAuth callback
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
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        try {
            $result = $this->kindeClient->getPermission($permission);
            // SDK returns: ["orgCode" => "org_1234", "isGranted" => true]
            return $result['isGranted'] ?? false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get a specific claim value
     */
    public function getClaim(string $claimName, string $tokenType = 'access_token'): mixed
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        try {
            $result = $this->kindeClient->getClaim($claimName, $tokenType);
            // SDK returns: ["name" => "aud", "value" => ["api.yourapp.com"]]
            return $result['value'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get current organization details
     */
    public function getOrganization(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        try {
            $result = $this->kindeClient->getOrganization();
            // SDK returns: ["orgCode" => "org_1234"]
            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Direct access to the raw Kinde client for advanced usage
     * This allows developers to access any SDK method not wrapped here
     */
    public function getRawClient(): KindeClientSDK
    {
        return $this->kindeClient;
    }
} 