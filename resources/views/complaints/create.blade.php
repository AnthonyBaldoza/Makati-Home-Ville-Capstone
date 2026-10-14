<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File a Complaint</title>
</head>
<body>
    <h1>File a Complaint</h1>

    <form method="POST" action="{{ route('complaints.store') }}">
        @csrf

        <div>
            <label for="title">Title</label><br>
            <input type="text" id="title" name="title" value="{{ old('title') }}">
            @error('title') <p>{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="description">Description</label><br>
            <textarea id="description" name="description" rows="5">{{ old('description') }}</textarea>
            @error('description') <p>{{ $message }}</p> @enderror
        </div>

        <button type="submit">Submit</button>
    </form>

    <a href="{{ route('complaints.index') }}">Cancel</a>
</body>
</html>