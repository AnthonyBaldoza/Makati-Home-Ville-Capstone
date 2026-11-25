@extends('layouts.app')
@section('title', 'My Profile | Makati Home Ville')
@section('content')
    <div class="card profile">
        <div class="profile-head">
            <x-avatar :user="$user" size="lg" id="avatar-preview" />
            <div>
                <h1>{{ $user->name }}</h1>
                <p class="meta">{{ $user->role->name }}, {{ $user->email }}</p>
            </div>
        </div>

        @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label for="avatar">Profile picture</label>
            <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
            <p class="meta hint">JPG, PNG, or WebP. Up to 2 MB, at least 100 x 100 pixels.</p>
            @if ($user->avatar_path)
                <label class="check">
                    <input type="checkbox" name="remove_avatar" value="1"> Remove current picture
                </label>
            @endif

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>

            <label for="email">Email</label>
            <input type="email" id="email" value="{{ $user->email }}" disabled>
            <p class="meta hint">Your email and role cannot be changed here.</p>

            @if ($user->role->name === 'Resident')
                <div class="field-row">
                    <div>
                        <label for="block">Block</label>
                        <input type="text" id="block" name="block" value="{{ old('block', $user->block) }}" required>
                    </div>
                    <div>
                        <label for="lot">Lot</label>
                        <input type="text" id="lot" name="lot" value="{{ old('lot', $user->lot) }}" required>
                    </div>
                </div>
            @endif

            <label for="mobile_number">Mobile number (09XXXXXXXXX)</label>
            <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" required>

            <div class="actions">
                <button type="submit" class="btn">Save changes</button>
                <a href="/">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        // Show the chosen picture before saving
        document.getElementById('avatar').addEventListener('change', function () {
            var file = this.files[0];
            var box = document.getElementById('avatar-preview');
            if (!file || !file.type.startsWith('image/')) { return; }
            var img = document.createElement('img');
            img.alt = '';
            img.src = URL.createObjectURL(file);
            box.replaceChildren(img);
        });
    </script>
@endsection