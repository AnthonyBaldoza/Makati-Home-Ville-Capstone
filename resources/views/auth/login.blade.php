@extends('layouts.app')
@section('title', 'Login | Makati Home Ville')
@section('content')
    <div class="card auth">
        <h1>Login</h1>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Login</button>
        </form>

        <p><a href="{{ route('password.request') }}">Forgot password?</a></p>
        <p><a href="{{ route('register') }}">Create an account</a></p>
    </div>
@endsection