@extends('layouts.app')
@section('title', 'Reserve a Facility')
@section('content')
    <div class="card">
        <h1>Reserve a Facility</h1>

        <form method="POST" action="{{ route('reservations.store') }}">
            @csrf

            <label for="facility_id">Facility</label>
            <select id="facility_id" name="facility_id">
                @foreach ($facilities as $facility)
                    <option value="{{ $facility->id }}" @selected(old('facility_id') == $facility->id)>{{ $facility->name }}
                    </option>
                @endforeach
            </select>
            @error('facility_id')
            <p class="error">{{ $message }}</p> @enderror

            <label for="reservation_date">Date</label>
            <input type="date" id="reservation_date" name="reservation_date" value="{{ old('reservation_date') }}">
            @error('reservation_date')
            <p class="error">{{ $message }}</p> @enderror

            <label for="start_time">Start time</label>
            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}">
            @error('start_time')
            <p class="error">{{ $message }}</p> @enderror

            <label for="end_time">End time</label>
            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}">
            @error('end_time')
            <p class="error">{{ $message }}</p> @enderror

            <label for="purpose">Purpose</label>
            <input type="text" id="purpose" name="purpose" value="{{ old('purpose') }}">
            @error('purpose')
            <p class="error">{{ $message }}</p> @enderror

            <div class="actions">
                <button type="submit" class="btn">Submit request</button>
                <a href="{{ route('reservations.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection