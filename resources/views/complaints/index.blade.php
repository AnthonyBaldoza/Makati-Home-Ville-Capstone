<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaints</title>
</head>
<body>
    <h1>Complaints</h1>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <a href="{{ route('complaints.create') }}">File a complaint</a>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Title</th>
                <th>Filed by</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->title }}</td>
                    <td>{{ $complaint->user->name }}</td>
                    <td>{{ $complaint->status }}</td>
                    <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                    <td><a href="{{ route('complaints.show', $complaint) }}">View</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No complaints yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="/">Back to home</a>
</body>
</html>