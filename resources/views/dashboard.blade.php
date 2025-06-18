@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="card start-hero">
        <p class="text-body-2 start-hero-intro">Woohoo!</p>
        <p class="text-display-2">
            Your authentication is all sorted.<br>
            Build the important stuff.
        </p>
    </div>
    
    <section class="next-steps-section">
        <h2 class="text-heading-1">Next steps for you</h2>
        
        @php
            $kindeService = app(\App\Services\KindeService::class);
            $authUser = $kindeService->getUser();
        @endphp
        
        @if($authUser)
            <div style="margin-top: var(--g-spacing-large); padding: var(--g-spacing-large); background: var(--g-color-grey-100); border-radius: var(--g-border-radius-base);">
                <h3 class="text-heading-2" style="margin-bottom: var(--g-spacing-base);">User Profile</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--g-spacing-base);">
                    <div>
                        <strong>Name:</strong><br>
                        <span class="text-subtle">{{ $authUser->given_name }} {{ $authUser->family_name }}</span>
                    </div>
                    <div>
                        <strong>Email:</strong><br>
                        <span class="text-subtle">{{ $authUser->email }}</span>
                    </div>
                    <div>
                        <strong>ID:</strong><br>
                        <span class="text-subtle" style="font-family: monospace; font-size: var(--g-font-size-x-small);">{{ $authUser->id }}</span>
                    </div>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection 