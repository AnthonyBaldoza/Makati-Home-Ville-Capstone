<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $complaint->title }}</title>
</head>
<body>
    <h1>{{ $complaint->title }}</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <p><strong>Filed by:</strong> {{ $complaint->user->name }}</p>
    <p><strong>Date:</strong> {{ $complaint->created_at->format('M d, Y') }}</p>
    <p><strong>Status:</strong> {{ $complaint->status }}</p>
    <p>{{ $complaint->description }}</p>

    @if (auth()->user()->role->name === 'HOA Admin')
        <form method="POST" action="{{ route('complaints.status', $complaint) }}">
            @csrf
            @method('PUT')

            <label for="status">Update status</label>
            <select id="status" name="status">
                @foreach (['Pending', 'In Progress', 'Resolved'] as $status)
                    <option value="{{ $status }}" @selected($complaint->status === $status)>{{ $status }}</option>
                @endforeach
            </select>
            @error('status') <p>{{ $message }}</p> @enderror

            <button type="submit">Save</button>
        </form>
    @endif

    <a href="{{ route('complaints.index') }}">Back to complaints</a>
</body>
</html>