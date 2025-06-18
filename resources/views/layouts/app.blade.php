<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Kinde Authentication')</title>
    
    @vite(['resources/css/app.css', 'resources/css/kinde.css', 'resources/js/app.js'])
</head>
<body>
    <header>
        <nav class="nav container">
            <h1 class="text-display-3">KindeAuth</h1>
            <div>
                @php
                    $kindeService = app(\App\Services\KindeService::class);
                    $isAuthenticated = $kindeService->isAuthenticated();
                    $authUser = $isAuthenticated ? $kindeService->getUser() : null;
                @endphp
                
                @if(!$isAuthenticated)
                    <a href="{{ route('auth.login') }}" class="btn btn-ghost sign-in-btn">
                        Sign in
                    </a>
                    <a href="{{ route('auth.register') }}" class="btn btn-dark">
                        Sign up
                    </a>
                @else
                    <div class="profile-blob">
                        @if(isset($authUser->picture))
                            <img class="avatar" src="{{ $authUser->picture }}" alt="user profile avatar" referrerpolicy="no-referrer">
                        @else
                            <div class="avatar">
                                {{ strtoupper(substr($authUser->given_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($authUser->family_name ?? '', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-heading-2">
                                {{ $authUser->given_name }} {{ $authUser->family_name }}
                            </p>
                            <a href="{{ route('auth.logout') }}" class="text-subtle">Log out</a>
                        </div>
                    </div>
                @endif
            </div>
        </nav>
    </header>

    <main>
        @if(session('success'))
            <div class="container">
                <div style="margin-bottom: 1rem; padding: 1rem; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 0.5rem;">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="container">
                <div style="margin-bottom: 1rem; padding: 1rem; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; border-radius: 0.5rem;">
                    {{ session('error') }}
                </div>
            </div>
        @endif
        
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <strong class="text-heading-2">KindeAuth</strong>
            <p class="footer-tagline text-body-3">
                Visit our <a class="link" href="https://kinde.com/docs">help center</a>
            </p>
            <small class="text-subtle">
                © 2023 KindeAuth, Inc. All rights reserved
            </small>
        </div>
    </footer>
</body>
</html> 