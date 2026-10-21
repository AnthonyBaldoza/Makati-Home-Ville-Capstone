<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Reservation::with(['facility', 'user'])->latest();

        if ($user->role->name === 'Resident') {
            $query->where('user_id', $user->id);
        }

        $reservations = $query->get();

        return view('reservations.index', compact('reservations'));
    }

    public function create()
    {
        $facilities = Facility::orderBy('name')->get();

        return view('reservations.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose' => ['required', 'string', 'max:255'],
        ]);

        $taken = Reservation::approvedOverlapping(
            $validated['facility_id'],
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time']
        )->exists();

        if ($taken) {
            throw ValidationException::withMessages([
                'start_time' => 'That time slot is already booked. Please pick another time.',
            ]);
        }

        $request->user()->reservations()->create($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation request sent. Please wait for approval.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Approved', 'Declined'])],
        ]);

        if ($validated['status'] === 'Approved') {
            $taken = Reservation::approvedOverlapping(
                $reservation->facility_id,
                $reservation->reservation_date->toDateString(),
                $reservation->start_time,
                $reservation->end_time
            )->where('id', '!=', $reservation->id)->exists();

            if ($taken) {
                return back()->withErrors([
                    'status' => 'Cannot approve: another approved reservation overlaps this time.',
                ]);
            }
        }

        $reservation->status = $validated['status'];
        $reservation->save();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation '.strtolower($validated['status']).'.');
    }
}