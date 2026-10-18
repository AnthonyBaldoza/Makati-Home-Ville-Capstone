@extends('layouts.app')
@section('title', $announcement->title . ' | Makati Home Ville')
@section('content')
    <div class="card">
        <h1>{{ $announcement->title }}</h1>
        <small class="meta">By {{ $announcement->user->name }} on {{ $announcement->created_at->format('M d, Y') }}</small>
        <p>{{ $announcement->body }}</p>

        @if (auth()->user()->role->name === 'HOA Admin')
            <div class="actions">
                <a class="btn" href="{{ route('announcements.edit', $announcement) }}">Edit</a>

                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this announcement?')">Delete</button>
                </form>
            </div>
        @endif
    </div>

    <a href="{{ route('announcements.index') }}">Back</a>
@endsection