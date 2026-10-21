@extends('layouts.app')
@section('title', 'Reset password | Makati Home Ville')
@section('content')
    <div class="card auth">
        <h1>Reset password</h1>

        @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>

            <label for="password">New password</label>
            <x-password-input id="password" name="password" autocomplete="new-password" />

            <label for="password_confirmation">Confirm new password</label>
            <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" />

            <button type="submit" class="btn">Reset password</button>
        </form>
    </div>
@endsection