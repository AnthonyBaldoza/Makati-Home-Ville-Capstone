<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $announcement->title }} | Makati Home Ville</title>
</head>
<body>
    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <h1>{{ $announcement->title }}</h1>
    <small>By {{ $announcement->user->name }} on {{ $announcement->created_at->format('M d, Y') }}</small>
    <p>{{ $announcement->body }}</p>

    @if (auth()->user()->role->name === 'HOA Admin')
        <a href="{{ route('announcements.edit', $announcement) }}">Edit</a>

        <form method="POST" action="{{ route('announcements.destroy', $announcement) }}">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Delete this announcement?')">Delete</button>
        </form>
    @endif

    <a href="{{ route('announcements.index') }}">Back</a>
</body>
</html>
