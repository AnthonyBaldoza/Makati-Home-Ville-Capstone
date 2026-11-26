@extends('layouts.app')
@section('title', 'Notifications | Makati Home Ville')
@section('content')
    <div class="card notifications">
        <div class="notif-head">
            <h1>Notifications</h1>
            @if ($notifications->whereNull('read_at')->isNotEmpty())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm">Mark all as read</button>
                </form>
            @endif
        </div>

        @forelse ($notifications as $notification)
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="notif-row {{ $notification->read_at ? '' : 'unread' }}">
                @csrf
                <button type="submit" class="notif-item">
                    <span class="notif-dot" aria-hidden="true"></span>
                    <span>
                        <span class="notif-msg">{{ $notification->data['message'] ?? 'You have a new notification.' }}</span>
                        <span class="meta notif-time">
                            {{ $notification->created_at->diffForHumans() }}@unless ($notification->read_at), unread @endunless
                        </span>
                    </span>
                </button>
            </form>
        @empty
            <p>No notifications yet. You will see updates here when a complaint status changes or a reservation is reviewed.</p>
        @endforelse
    </div>
@endsection