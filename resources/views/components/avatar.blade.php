@props(['user', 'size' => 'md'])
@php
    $initials = \Illuminate\Support\Str::of($user->name)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp
<span {{ $attributes->class(['avatar', 'avatar-'.$size]) }}>
    @if ($user->avatar_path)
        <img src="{{ route('avatars.show', $user) }}?v={{ $user->updated_at?->timestamp }}" alt="">
    @else
        <span aria-hidden="true">{{ $initials }}</span>
    @endif
</span>