@extends('layouts.app')
@section('title', 'New Announcement | Makati Home Ville')
@section('content')
    <div class="card">
        <h1>New Announcement</h1>

        @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('announcements.store') }}">
            @csrf
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>

            <label for="body">Body</label>
            <textarea id="body" name="body" rows="6" required>{{ old('body') }}</textarea>

            <div class="actions">
                <button type="submit" class="btn">Post</button>
                <a href="{{ route('announcements.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection