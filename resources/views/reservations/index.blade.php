@extends('layouts.app')
@section('title', 'Reservations')
@section('content')
    <div class="card">
        <h1>Facility Reservations</h1>
        <a class="btn" href="{{ route('reservations.create') }}">Reserve a facility</a>
    </div>

    @if ($errors->any())
        <p class="error">{{ $errors->first() }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Facility</th>
                <th>Reserved by</th>
                <th>Date</th>
                <th>Time</th>
                <th>Purpose</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->facility->name }}</td>
                    <td>{{ $reservation->user->name }}</td>
                    <td>{{ $reservation->reservation_date->format('M d, Y') }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($reservation->start_time)->format('g:i A') }}
                        -
                        {{ \Carbon\Carbon::parse($reservation->end_time)->format('g:i A') }}
                    </td>
                    <td>{{ $reservation->purpose }}</td>
                    <td><span class="badge">{{ $reservation->status }}</span></td>
                    <td>
                        @if (auth()->user()->role->name === 'Facility Staff' && $reservation->status === 'Pending')
                            <form method="POST" action="{{ route('reservations.status', $reservation) }}" style="display:inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="btn">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('reservations.status', $reservation) }}" style="display:inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="Declined">
                                <button type="submit" class="btn btn-outline">Decline</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No reservations yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection