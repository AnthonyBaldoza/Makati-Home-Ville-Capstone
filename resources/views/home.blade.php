@extends('layouts.app')
@section('title', 'Home')
@section('content')
    <div class="card">
        <h1>Welcome, {{ auth()->user()->name }}</h1>
        <p>Role: {{ auth()->user()->role->name }}</p>
        @if (auth()->user()->role->name === 'HOA Admin')
            <p>You are an admin. You can post announcements and update complaint statuses.</p>
        @endif
    </div>
@endsection