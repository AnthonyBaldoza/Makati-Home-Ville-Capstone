@extends('layouts.app')
@section('title', $complaint->title)
@section('content')
    <div class="card">
        <h1>{{ $complaint->title }}</h1>
        <p><strong>Filed by:</strong> {{ $complaint->user->name }}</p>
        <p><strong>Date:</strong> {{ $complaint->created_at->format('M d, Y') }}</p>
        <p><strong>Status:</strong> <span class="badge">{{ $complaint->status }}</span></p>
        <p>{{ $complaint->description }}</p>

        @if ($complaint->attachments->isNotEmpty())
            <div class="attachments">
                @foreach ($complaint->attachments as $file)
                    @if ($file->isVideo())
                        <video controls preload="metadata" src="{{ route('attachments.show', $file) }}"></video>
                    @else
                        <img src="{{ route('attachments.show', $file) }}" alt="{{ $file->original_name }}" loading="lazy">
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @if (auth()->user()->role->name === 'HOA Admin')
        <div class="card">
            <form method="POST" action="{{ route('complaints.status', $complaint) }}">
                @csrf
                @method('PUT')

                <label for="status">Update status</label>
                <select id="status" name="status">
                    @foreach (\App\Models\Complaint::STATUSES as $status)
                        <option value="{{ $status }}" @selected($complaint->status === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status') <p class="error">{{ $message }}</p> @enderror

                <button type="submit" class="btn">Save</button>
            </form>
        </div>
    @endif

    <a href="{{ route('complaints.index') }}">Back to complaints</a>
@endsection