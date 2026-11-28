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
@php
    // HOA Admin and Facility Staff get the sidebar layout. Residents keep the top bar.
    $useSidebar = auth()->check() && \App\Support\SlotRules::isStaff(auth()->user());
@endphp
<body class="{{ $useSidebar ? 'has-sidebar' : '' }}">
@if ($useSidebar)
    @php
        $nav = [
            ['Dashboard', url('/'), 'dashboard', request()->is('/')],
            ['Announcements', route('announcements.index'), 'megaphone', request()->routeIs('announcements.*')],
            ['Complaints', route('complaints.index'), 'alert', request()->routeIs('complaints.*')],
            ['Reservations', route('reservations.index'), 'calendar', request()->routeIs('reservations.*')],
            ['Events', route('community-events.index'), 'users', request()->routeIs('community-events.*')],
            ['Notifications', route('notifications.index'), 'bell', request()->routeIs('notifications.*')],
        ];
        $pageName = \Illuminate\Support\Str::before($__env->yieldContent('title', 'Dashboard'), ' | ');
    @endphp
    <div class="shell">
        <aside class="sidebar" id="sidebar" aria-label="Main navigation">
            <a class="side-brand" href="{{ url('/') }}">
                <span class="side-mark" aria-hidden="true">M</span>
                <span>
                    <strong>Makati Home Ville</strong>
                    <small>{{ auth()->user()->role->name }}</small>
                </span>
            </a>

            <nav class="side-nav">
                @foreach ($nav as [$label, $href, $icon, $active])
                    <a href="{{ $href }}" @class(['active' => $active]) @if ($active) aria-current="page" @endif>
                        <x-icon :name="$icon" />
                        <span>{{ $label }}</span>
                    </a>
                @endforeach

                <p class="side-heading">Account</p>
                <a href="{{ route('profile.edit') }}" @class(['active' => request()->routeIs('profile.*')]) @if (request()->routeIs('profile.*')) aria-current="page" @endif>
                    <x-icon name="user" />
                    <span>Profile</span>
                </a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="side-logout">
                @csrf
                <button type="submit">
                    <x-icon name="logout" />
                    <span>Logout</span>
                </button>
            </form>
        </aside>
        <div class="side-backdrop" id="sideBackdrop" hidden></div>

        <div class="shell-main">
            <header class="shell-top">
                <button type="button" class="menu-btn" id="menuBtn" aria-label="Open menu" aria-controls="sidebar" aria-expanded="false">
                    <x-icon name="menu" />
                </button>
                <p class="crumbs">Pages / <strong>{{ $pageName }}</strong></p>
                <div class="shell-user">
                    <x-notification-bell />
                    <a class="profile-link" href="{{ route('profile.edit') }}" aria-label="My profile: {{ auth()->user()->name }}">
                        <x-avatar :user="auth()->user()" size="sm" />
                        <span class="who">{{ auth()->user()->name }}</span>
                    </a>
                </div>
            </header>

            @yield('alert')

            <main class="container">
                @if (session('success'))
                    <div class="flash">{{ session('success') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            var body = document.body;
            var btn = document.getElementById('menuBtn');
            var backdrop = document.getElementById('sideBackdrop');
            function setOpen(open) {
                body.classList.toggle('side-open', open);
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                backdrop.hidden = !open;
            }
            btn.addEventListener('click', function () { setOpen(!body.classList.contains('side-open')); });
            backdrop.addEventListener('click', function () { setOpen(false); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        })();
    </script>
@else
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
@endif
</body>
</html>