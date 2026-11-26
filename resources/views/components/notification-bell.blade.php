@php
    $unread = auth()->user()->unreadNotifications()->count();
@endphp
<a class="bell" href="{{ route('notifications.index') }}"
   aria-label="Notifications{{ $unread ? ': '.$unread.' unread' : '' }}"
   title="Notifications">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8"/>
        <path d="M10 21a2 2 0 0 0 4 0"/>
    </svg>
    @if ($unread)
        <span class="bell-count" aria-hidden="true">{{ $unread > 99 ? '99+' : $unread }}</span>
    @endif
</a>