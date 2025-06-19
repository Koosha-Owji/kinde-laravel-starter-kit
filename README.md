# Kinde Laravel Authentication Starter Kit

A complete Laravel authentication starter kit using [Kinde](https://kinde.com) for secure, modern user authentication. This project provides a ready-to-use foundation for Laravel applications that need robust authentication with minimal setup.

## ✨ Features

- **🔐 Complete OAuth2 Authentication Flow** - Login, registration, logout with Kinde
- **🛡️ Route Protection** - Middleware-based authentication for protected routes
- **⚡ Laravel Integration** - Native Laravel service container and middleware
- **🔧 Flexible Architecture** - Simplified service with direct SDK access when needed

## 🚀 Quick Start

### Prerequisites

- PHP 8.1 or higher
- Laravel 11.x
- Composer
- A [Kinde](https://kinde.com) account

### Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd laravel-kinde-starter
   ```
2. **Install dependencies**

   ```bash
   composer install
   npm install
   ```
3. **Set up environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Configure Kinde**

   Create an application in your [Kinde dashboard](https://app.kinde.com) and add these values to your `.env` file:

   ```env
   KINDE_DOMAIN=your-kinde-domain.kinde.com
   KINDE_CLIENT_ID=your_client_id
   KINDE_CLIENT_SECRET=your_client_secret
   KINDE_REDIRECT_URL=http://localhost:8000/auth/callback
   KINDE_POST_LOGOUT_REDIRECT_URL=http://localhost:8000
   ```
5. **Configure Kinde URLs**

   In your Kinde app settings, add:

   - **Allowed callback URLs**: `http://localhost:8000/auth/callback`
   - **Allowed logout redirect URLs**: `http://localhost:8000`
6. **Build assets and start the server**

   ```bash
   npm run build
   php artisan serve
   ```

Visit `http://localhost:8000` to see your authentication-ready Laravel app!

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php      # Authentication routes handler
│   └── Middleware/
│       └── KindeAuth.php           # Route protection middleware
├── Providers/
│   ├── AppServiceProvider.php     # Global view data sharing
│   └── KindeServiceProvider.php   # Kinde service registration
└── Services/
    └── KindeService.php            # Simplified Kinde SDK wrapper

config/
└── services.php                   # Kinde configuration

resources/
├── css/
│   ├── app.css                    # Application styles
│   └── kinde.css                  # Kinde design system
└── views/
    ├── layouts/
    │   └── app.blade.php          # Main layout template
    ├── welcome.blade.php          # Landing page
    └── dashboard.blade.php        # Protected dashboard

routes/
└── web.php                       # Application routes
```

## 🛠️ Available Routes

| Route              | Method | Description                    | Protected |
| ------------------ | ------ | ------------------------------ | --------- |
| `/`              | GET    | Home/Welcome page              | No        |
| `/auth/login`    | GET    | Redirect to Kinde login        | No        |
| `/auth/register` | GET    | Redirect to Kinde registration | No        |
| `/auth/callback` | GET    | OAuth callback handler         | No        |
| `/auth/logout`   | GET    | Logout and redirect to Kinde   | No        |
| `/dashboard`     | GET    | User dashboard                 | Yes       |

## 🧩 Key Components

### KindeService

The main service class providing essential authentication functionality:

- User authentication status checking
- User profile retrieval
- Basic permission checking
- OAuth URL generation (login/register)
- Logout handling
- Direct SDK access for advanced features

```php
$kindeService = app(KindeService::class);

// Check authentication
if ($kindeService->isAuthenticated()) {
    $user = $kindeService->getUser();
}

// Check permissions
if ($kindeService->hasPermission('create:posts')) {
    // User can create posts
}

// Direct SDK access for advanced features
$client = $kindeService->client();
$organizations = $client->getUserOrganizations();
```

### KindeAuth Middleware

Protects routes requiring authentication:

```php
// Protect individual routes
Route::get('/protected', function () {
    return 'This is protected!';
})->middleware('kinde.auth');

// Protect route groups
Route::middleware('kinde.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

### AuthController

Handles all authentication flows:

- Login/registration redirects
- OAuth callback processing
- Logout handling
- Protected route access

## 🔧 Configuration

All Kinde configuration is stored in `config/services.php`:

```php
'kinde' => [
    'domain' => env('KINDE_DOMAIN'),
    'client_id' => env('KINDE_CLIENT_ID'),
    'client_secret' => env('KINDE_CLIENT_SECRET'),
    'redirect_url' => env('KINDE_REDIRECT_URL'),
    'post_logout_redirect_url' => env('KINDE_POST_LOGOUT_REDIRECT_URL'),
],
```

## 📚 Usage Examples

### Basic Authentication Check

```php
// In a controller
public function dashboard()
{
    $kindeService = app(KindeService::class);
  
    if (!$kindeService->isAuthenticated()) {
        return redirect()->route('auth.login');
    }
  
    $user = $kindeService->getUser();
    return view('dashboard', compact('user'));
}
```

### Permission-Based Access

```php
// Check specific permission
if ($kindeService->hasPermission('admin:users')) {
    // Show admin interface
}

// In Blade templates
@if($kindeService->hasPermission('create:posts'))
    <a href="{{ route('posts.create') }}">Create Post</a>
@endif
```

### Advanced SDK Usage

```php
// Access full Kinde PHP SDK functionality
$client = $kindeService->client();

// Get user organizations
$organizations = $client->getUserOrganizations();

// Get custom claims
$department = $client->getClaim('department', 'id_token');

// Get all permissions
$permissions = $client->getPermissions();
```

### Global View Data

User authentication data is automatically available in all views:

```blade
{{-- Available in all Blade templates --}}
@if($isAuthenticated)
    <p>Welcome, {{ $authUser->given_name }}!</p>
    <a href="{{ route('auth.logout') }}">Logout</a>
@else
    <a href="{{ route('auth.login') }}">Login</a>
@endif
```

## 🔒 Security Features

- **CSRF Protection** - Laravel's built-in CSRF protection
- **Secure Token Storage** - Tokens stored securely via Kinde SDK
- **Route Protection** - Middleware-based authentication
- **Session Management** - Proper session handling and cleanup
- **Error Handling** - Graceful error handling for auth failures

## 🚀 Deployment

### Environment Variables

Ensure these environment variables are set in production:

```env
KINDE_DOMAIN=your-production-domain.kinde.com
KINDE_CLIENT_ID=your_production_client_id
KINDE_CLIENT_SECRET=your_production_client_secret
KINDE_REDIRECT_URL=https://yourapp.com/auth/callback
KINDE_POST_LOGOUT_REDIRECT_URL=https://yourapp.com
```

### Kinde Configuration

Update your Kinde app settings with production URLs:

- **Allowed callback URLs**: `https://yourapp.com/auth/callback`
- **Allowed logout redirect URLs**: `https://yourapp.com`
