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

        <form method="POST" action="{{ route('announcements.update', $announcement) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>

            <label for="body">Body</label>
            <textarea id="body" name="body" rows="6" required>{{ old('body', $announcement->body) }}</textarea>

            @if ($announcement->attachments->isNotEmpty())
                <p><strong>Current attachments</strong> (check to remove)</p>
                <div class="attachments">
                    @foreach ($announcement->attachments as $file)
                        <label class="attachment-item">
                            @if ($file->isVideo())
                                <video controls preload="metadata" src="{{ route('attachments.show', $file) }}"></video>
                            @else
                                <img src="{{ route('attachments.show', $file) }}" alt="{{ $file->original_name }}" loading="lazy">
                            @endif
                            <span>
                                <input type="checkbox" name="remove_media[]" value="{{ $file->id }}">
                                Remove {{ $file->original_name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif

            <label for="media">Add photos or videos (optional, up to 5, 20 MB each)</label>
            <input type="file" id="media" name="media[]" accept="image/*,video/*" multiple>

            <div class="actions">
                <button type="submit" class="btn">Update</button>
                <a href="{{ route('announcements.show', $announcement) }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection