@extends('layouts.app')
@section('title', 'Complaints')
@section('content')
    <div class="card">
        <h1>Complaints</h1>
        <a class="btn" href="{{ route('complaints.create') }}">File a complaint</a>
    </div>

    <table>
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
                    <td><span class="badge">{{ $complaint->status }}</span></td>
                    <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                    <td><a href="{{ route('complaints.show', $complaint) }}">View</a></td>
                </tr>
            @empty
                <tr><td colspan="5">No complaints yet.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection