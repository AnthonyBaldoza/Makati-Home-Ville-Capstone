@extends('layouts.app')
@section('title', 'Home')

@section('content')
    <div class="intro">
        <h1>Welcome, {{ auth()->user()->name }}</h1>
        <p>Role: {{ auth()->user()->role->name }}. Community news and complaints.</p>
    </div>

    <div class="dash">
        <section class="panel">
            <div class="panel-head">
                <h2>Announcements</h2>
                <span class="panel-links">
                    @if ($isAdmin)
                        <a href="{{ route('announcements.create') }}">New announcement</a>
                    @endif
                    <a href="{{ route('announcements.index') }}">View all</a>
                </span>
            </div>
            <ul class="feed">
                @forelse ($announcements as $announcement)
                    <li>
                        <time datetime="{{ $announcement->created_at->toDateString() }}">{{ $announcement->created_at->format('M d') }}</time>
                        <div>
                            <h3><a href="{{ route('announcements.show', $announcement) }}">{{ $announcement->title }}</a></h3>
                            <p>{{ \Illuminate\Support\Str::limit($announcement->body, 110) }}</p>
                        </div>
                    </li>
                @empty
                    <li><p>Wala pang announcement.</p></li>
                @endforelse
            </ul>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>File a complaint</h2>
            </div>
            <form method="POST" action="{{ route('complaints.store') }}">
                @csrf

                <label for="d-title">Title</label>
                <input type="text" id="d-title" name="title" value="{{ old('title') }}">
                @error('title') <p class="error">{{ $message }}</p> @enderror

                <label for="d-description">Description</label>
                <textarea id="d-description" name="description" rows="4" placeholder="Describe the problem and where it happens">{{ old('description') }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror

                <button type="submit" class="btn btn-block">Submit complaint</button>
            </form>
        </section>
    </div>

    <section class="panel">
        <div class="panel-head">
            <h2>Complaints</h2>
            <span class="panel-links">
                <a href="{{ route('complaints.index') }}">View all</a>
            </span>
        </div>
        <div class="table-scroll">
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
                            <td><span class="status {{ $complaint->statusClass() }}">{{ $complaint->status }}</span></td>
                            <td>{{ $complaint->created_at->format('M d, Y') }}</td>
                            <td><a href="{{ route('complaints.show', $complaint) }}">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No complaints yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
