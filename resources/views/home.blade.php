<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home | Makati Home Ville</title>
</head>
<body>
    <h1>Welcome, {{ auth()->user()->name }}</h1>
    <p>Role: {{ auth()->user()->role->name }}</p>

    <nav>
        <a href="{{ route('announcements.index') }}">Announcements</a>
        <a href="{{ route('complaints.index') }}">Complaints</a>
    </nav>

    @if (auth()->user()->role->name === 'HOA Admin')
        <p>You are an admin. Admin tools will appear here.</p>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>