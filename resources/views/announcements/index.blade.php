<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements | Makati Home Ville</title>
</head>
<body>
    <h1>Announcements</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (auth()->user()->role->name === 'HOA Admin')
        <a href="{{ route('announcements.create') }}">New Announcement</a>
    @endif

    @forelse ($announcements as $announcement)
        <div>
            <h2>
                <a href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a>
            </h2>
            <small>By {{ $announcement->user->name }} on {{ $announcement->created_at->format('M d, Y') }}</small>
        </div>
    @empty
        <p>No announcements yet.</p>
    @endforelse

    <a href="/">Back to home</a>
</body>
</html>