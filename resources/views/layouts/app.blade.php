<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Makati Home Ville')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="/">Makati Home Ville</a>
        @auth
            <nav>
                <a href="{{ route('announcements.index') }}">Announcements</a>
                <a href="{{ route('complaints.index') }}">Complaints</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <span class="who">{{ auth()->user()->name }} ({{ auth()->user()->role->name }})</span>
                <button type="submit" class="btn btn-outline">Logout</button>
            </form>
        @endauth
    </header>

    <main class="container">
        @if (session('success'))
            <div class="flash">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>