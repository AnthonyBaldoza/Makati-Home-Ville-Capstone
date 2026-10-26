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
            <label for="role">Register as</label>
            <select id="role" name="role" required>
                @foreach ($roles as $roleName)
                    <option value="{{ $roleName }}" @selected(old('role', 'Resident') === $roleName)>{{ $roleName }}</option>
                @endforeach
            </select>

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>

            <div id="resident-fields">
                <label for="block">Block</label>
                <input type="text" id="block" name="block" value="{{ old('block') }}">

                <label for="lot">Lot</label>
                <input type="text" id="lot" name="lot" value="{{ old('lot') }}">
            </div>

            <label for="mobile_number">Mobile Number (09XXXXXXXXX)</label>
            <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required>

            <div id="code-field" hidden>
                <label for="access_code">Access code (From the HOA)</label>
                <input type="password" id="access_code" name="access_code" autocomplete="off">
            </div>

            <label for="password">Password</label>
            <x-password-input id="password" name="password" autocomplete="new-password" />

            <label for="password_confirmation">Confirm Password</label>
            <x-password-input id="password_confirmation" name="password_confirmation" autocomplete="new-password" />

            <button type="submit" class="btn">Register</button>
        </form>

        <p><a href="{{ route('login') }}">Already have an account?</a></p>
    </div>

    <script>
        const roleSelect = document.getElementById('role');

        function toggleFields() {
            const isResident = roleSelect.value === 'Resident';
            document.getElementById('resident-fields').hidden = !isResident;
            document.getElementById('code-field').hidden = isResident;
        }

        roleSelect.addEventListener('change', toggleFields);
        toggleFields();
    </script>
@endsection