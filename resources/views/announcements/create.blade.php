<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Announcement | Makati Home Ville</title>
</head>
<body>
    <h1>New Announcement</h1>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('announcements.store') }}">
        @csrf
        <label>Title</label>
        <input type="text" name="title" value="{{ old('title') }}" required>

        <label>Body</label>
        <textarea name="body" rows="6" required>{{ old('body') }}</textarea>

        <button type="submit">Post</button>
    </form>

    <a href="{{ route('announcements.index') }}">Cancel</a>
</body>
</html>