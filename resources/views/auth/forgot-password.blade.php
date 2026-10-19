@extends('layouts.app')
@section('title', 'Forgot password | Makati Home Ville')
@section('content')
    <div class="card auth">
        <h1>Forgot password</h1>
        <p>Enter your account's email address and we'll send you a link to choose a new password.</p>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>

            <button type="submit" class="btn">Send reset link</button>
        </form>

        <p><a href="{{ route('login') }}">Back to login</a></p>
    </div>
@endsection