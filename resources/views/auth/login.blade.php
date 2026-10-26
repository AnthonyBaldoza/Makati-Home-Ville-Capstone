
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
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <!-- Upang maging reusable ang password field sa iba't ibang pages -->
            <x-password-input 
                id="password"
                name="password"
                autocomplete="current-password"
            />

            <label for="role">Login as</label>
            <select id="role" name="role" required>
                <option value="">Select your role</option>
                <option value="HOA Admin" @selected(old('role') === 'HOA Admin')>HOA Admin</option>
                <option value="Resident" @selected(old('role') === 'Resident')>Resident</option>
                <option value="Facility Staff" @selected(old('role') === 'Facility Staff')>Facility Staff</option>
            </select>

            <button type="submit" class="btn">Login</button>
        </form>

        <p><a href="{{ route('password.request') }}">Forgot password?</a></p>
        <p><a href="{{ route('register') }}">Create an account</a></p>
    </div>
@endsection
