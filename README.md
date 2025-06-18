# Laravel Kinde Authentication Starter Kit

A complete Laravel starter kit demonstrating authentication integration with [Kinde](https://kinde.com), featuring modern UI with Tailwind CSS and comprehensive authentication flows.

## ✨ Features

- 🔐 **Complete OAuth 2.0 Authentication** with Kinde
- 🎨 **Modern UI** with Tailwind CSS v4
- 🛡️ **Route Protection** with custom middleware  
- 👤 **User Profile Management** with permissions and organizations
- 🚀 **Laravel 11** with Vite integration
- 📱 **Responsive Design** for mobile and desktop
- 🔧 **Service Provider** for dependency injection
- ✅ **Error Handling** with user-friendly messages

## 🚀 Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and npm
- A [Kinde](https://kinde.com) account

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Koosha-Owji/kinde-laravel-starter-kit.git
   cd kinde-laravel-starter-kit
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Kinde credentials**
   
   Update your `.env` file with your Kinde application details:
   ```env
   KINDE_DOMAIN=https://yourdomain.kinde.com
   KINDE_CLIENT_ID=your_client_id
   KINDE_CLIENT_SECRET=your_client_secret
   KINDE_REDIRECT_URL=http://localhost:8000/auth/callback
   KINDE_POST_LOGOUT_REDIRECT_URL=http://localhost:8000
   ```

5. **Build assets and start the server**
   ```bash
   npm run dev
   php artisan serve
   ```

6. **Visit your application**
   
   Open [http://localhost:8000](http://localhost:8000) in your browser.

## 🔧 Configuration

### Kinde Application Setup

1. Create a new application in your [Kinde dashboard](https://app.kinde.com)
2. Set the following URLs in your Kinde app settings:
   - **Allowed callback URLs**: `http://localhost:8000/auth/callback`
   - **Allowed logout redirect URLs**: `http://localhost:8000`
3. Copy your credentials to the `.env` file

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php      # Authentication routes handler
│   └── Middleware/
│       └── KindeAuth.php           # Route protection middleware
├── Providers/
│   └── KindeServiceProvider.php    # Service container registration
└── Services/
    └── KindeService.php            # Kinde SDK wrapper service

resources/
├── css/
│   └── app.css                     # Tailwind CSS configuration
└── views/
    ├── layouts/
    │   └── app.blade.php           # Main layout template
    ├── welcome.blade.php           # Landing page
    └── dashboard.blade.php         # Protected dashboard

routes/
└── web.php                        # Application routes
```

## 🛠️ Available Routes

| Route | Method | Description | Protected |
|-------|--------|-------------|-----------|
| `/` | GET | Home/Welcome page | No |
| `/auth/login` | GET | Redirect to Kinde login | No |
| `/auth/register` | GET | Redirect to Kinde registration | No |
| `/auth/callback` | GET | OAuth callback handler | No |
| `/auth/logout` | GET | Logout and redirect to Kinde | No |
| `/dashboard` | GET | User dashboard | Yes |

## 🧩 Key Components

### KindeService

The main service class providing:
- User authentication status
- User profile data
- Permissions and organizations
- Custom claims access
- URL generation for auth flows

### KindeAuth Middleware

Protects routes requiring authentication:
- Redirects unauthenticated users to login
- Stores intended URL for post-login redirect
- Handles both web and API requests

### AuthController

Handles all authentication flows:
- Login/Register redirects
- OAuth callback processing  
- Error handling
- Dashboard display

## 🎨 Customization

### Styling

The starter kit uses Tailwind CSS v4. Customize styles in:
- `resources/css/app.css` - Main stylesheet
- Blade templates - Component-specific styles

### Adding Features

Extend the starter kit by:
1. Adding new routes in `routes/web.php`
2. Creating controllers for your features
3. Using the `KindeService` for user data
4. Applying `kinde.auth` middleware for protection

## 📚 Documentation

- [Kinde Documentation](https://kinde.com/docs)
- [Kinde PHP SDK](https://github.com/kinde-oss/kinde-auth-php)
- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

- [Kinde Community](https://kinde.com/community)
- [GitHub Issues](https://github.com/Koosha-Owji/kinde-laravel-starter-kit/issues)
- [Kinde Documentation](https://kinde.com/docs)
