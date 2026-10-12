<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Announcement | Makati Home Ville</title>
</head>
<body>
    <h1>Edit Announcement</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('announcements.update', $announcement) }}">
        @csrf
        @method('PUT')

        <label>Title</label>
        <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required>

        <label>Body</label>
        <textarea name="body" rows="6" required>{{ old('body', $announcement->body) }}</textarea>

        <button type="submit">Update</button>
    </form>

    <a href="{{ route('announcements.show', $announcement) }}">Cancel</a>
</body>
</html>