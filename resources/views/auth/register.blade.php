@extends('layouts.app')
@section('title', 'Register | Makati Home Ville')
@section('content')
    <div class="card auth">
        <h1>Register</h1>

        @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>

            <label for="block">Block</label>
            <input type="text" id="block" name="block" value="{{ old('block') }}" required>

            <label for="lot">Lot</label>
            <input type="text" id="lot" name="lot" value="{{ old('lot') }}" required>

            <label for="mobile_number">Mobile Number (09XXXXXXXXX)</label>
            <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <button type="submit" class="btn">Register</button>
        </form>

        <p><a href="{{ route('login') }}">Already have an account?</a></p>
    </div>
@endsection