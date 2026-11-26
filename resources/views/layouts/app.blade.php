<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Makati Home Ville')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="/">Makati Home Ville</a>
            @auth
                <nav>
                    <a href="{{ route('announcements.index') }}">Announcements</a>
                    <a href="{{ route('complaints.index') }}">Complaints</a>
                    <a href="{{ route('reservations.index') }}">Reservations</a>
                    <a href="{{ route('community-events.index') }}">Events</a>
                </nav>
                <div class="topbar-user">
                    <x-notification-bell />
                    <a class="profile-link" href="{{ route('profile.edit') }}" aria-label="My profile: {{ auth()->user()->name }}">
                        <x-avatar :user="auth()->user()" size="sm" />
                        <span class="who">{{ auth()->user()->name }} ({{ auth()->user()->role->name }})</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    @yield('alert')

    <main class="container">
        @if (session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>