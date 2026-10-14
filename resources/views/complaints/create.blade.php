@extends('layouts.app')
@section('title', 'File a Complaint')
@section('content')
    <div class="card">
        <h1>File a Complaint</h1>

        <form method="POST" action="{{ route('complaints.store') }}">
            @csrf

            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}">
            @error('title') <p class="error">{{ $message }}</p> @enderror

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5">{{ old('description') }}</textarea>
            @error('description') <p class="error">{{ $message }}</p> @enderror

            <div class="actions">
                <button type="submit" class="btn">Submit</button>
                <a href="{{ route('complaints.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection