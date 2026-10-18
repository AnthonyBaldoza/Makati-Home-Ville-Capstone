@extends('layouts.app')
@section('title', 'Edit Announcement | Makati Home Ville')
@section('content')
    <div class="card">
        <h1>Edit Announcement</h1>

        @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('announcements.update', $announcement) }}">
            @csrf
            @method('PUT')

            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>

            <label for="body">Body</label>
            <textarea id="body" name="body" rows="6" required>{{ old('body', $announcement->body) }}</textarea>

            <div class="actions">
                <button type="submit" class="btn">Update</button>
                <a href="{{ route('announcements.show', $announcement) }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection