@extends('layouts.app')
@section('title', 'Dashboard | Makati Home Ville')

@section('content')
    <div class="intro">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->name }}. Here is what needs attention today.</p>
    </div>

    <section class="stats" aria-label="Summary">
        @foreach ($stats as $stat)
            @if ($stat['href'])
                <a class="stat" href="{{ $stat['href'] }}">
            @else
                <div class="stat">
            @endif
                <div>
                    <p class="stat-label">{{ $stat['label'] }}</p>
                    <p class="stat-value">{{ $stat['value'] }}</p>
                    <p class="stat-hint">{{ $stat['hint'] }}</p>
                </div>
                <span @class(['stat-icon', 'warn' => $stat['warn']])><x-icon :name="$stat['icon']" /></span>
            @if ($stat['href'])
                </a>
            @else
                </div>
            @endif
        @endforeach
    </section>

    <div class="dash-grid">
        @if ($isAdmin)
            <section class="panel">
                <div class="panel-head">
                    <h2>Complaints by status</h2>
                    <span class="panel-links"><a href="{{ route('complaints.index') }}">View all</a></span>
                </div>
                @php $maxComplaints = max(1, (int) $complaintCounts->max()); @endphp
                <div class="bars">
                    @foreach (['Pending' => 's-pending', 'In Progress' => 's-progress', 'Resolved' => 's-done'] as $status => $class)
                        @php $n = (int) ($complaintCounts[$status] ?? 0); @endphp
                        <div class="bar-row">
                            <span class="bar-label">{{ $status }}</span>
                            <span class="bar-track"><span class="bar-fill {{ $class }}" style="width: {{ $n / $maxComplaints * 100 }}%"></span></span>
                            <span class="bar-num">{{ $n }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="panel">
            <div class="panel-head">
                <h2>Reservations by status</h2>
                <span class="panel-links"><a href="{{ route('reservations.calendar') }}">Open calendar</a></span>
            </div>
            @php $maxReservations = max(1, (int) $reservationCounts->max()); @endphp
            <div class="bars">
                @foreach (['Pending' => 's-pending', 'Approved' => 's-done', 'Declined' => 's-declined'] as $status => $class)
                    @php $n = (int) ($reservationCounts[$status] ?? 0); @endphp
                    <div class="bar-row">
                        <span class="bar-label">{{ $status }}</span>
                        <span class="bar-track"><span class="bar-fill {{ $class }}" style="width: {{ $n / $maxReservations * 100 }}%"></span></span>
                        <span class="bar-num">{{ $n }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Waiting for your review</h2>
                <span class="panel-links"><a href="{{ route('reservations.calendar') }}">Review</a></span>
            </div>
            <ul class="feed">
                @forelse ($pendingReservations as $r)
                    <li>
                        <time datetime="{{ \Carbon\Carbon::parse($r->reservation_date)->toDateString() }}">{{ \Carbon\Carbon::parse($r->reservation_date)->format('M d') }}</time>
                        <div>
                            <h3>{{ $r->facility->name ?? 'Facility' }}</h3>
                            <p>{{ $r->user->name ?? 'Resident' }}, {{ \App\Support\SlotRules::pretty($r->start_time) }} to {{ \App\Support\SlotRules::pretty($r->end_time) }}</p>
                        </div>
                    </li>
                @empty
                    <li><p>All caught up. No reservations are waiting for review.</p></li>
                @endforelse
            </ul>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Latest announcements</h2>
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
                    <li><p>No announcements yet.</p></li>
                @endforelse
            </ul>
        </section>
    </div>

    @if ($isAdmin)
        <section class="panel">
            <div class="panel-head">
                <h2>Recent complaints</h2>
                <span class="panel-links"><a href="{{ route('complaints.index') }}">View all</a></span>
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
    @endif
@endsection