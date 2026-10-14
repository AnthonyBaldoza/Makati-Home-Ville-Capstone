@extends('layouts.app')
@section('title', 'Announcements | Makati Home Ville')
@section('content')
    <div class="card">
        <h1>Announcements</h1>
        @if (auth()->user()->role->name === 'HOA Admin')
            <a class="btn" href="{{ route('announcements.create') }}">New Announcement</a>
        @endif
    </div>

    @forelse ($announcements as $announcement)
        <div class="card">
            <h2>
                <a href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a>
            </h2>
            <small class="meta">By {{ $announcement->user->name }} on {{ $announcement->created_at->format('M d, Y') }}</small>
        </div>
    @empty
        <div class="card"><p>No announcements yet.</p></div>
    @endforelse
@endsection