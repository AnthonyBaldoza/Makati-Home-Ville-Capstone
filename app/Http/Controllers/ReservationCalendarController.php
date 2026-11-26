<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\ReservationChatMessage;
use App\Notifications\ReservationReviewed;
use App\Support\SlotRules;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationCalendarController extends Controller
{
    public function index(Request $request)
    {
        return view('reservations.calendar', [
            'facilities' => Facility::orderBy('name')->get(['id', 'name']),
            'isStaff' => SlotRules::isStaff($request->user()),
        ]);
    }

    public function feed(Request $request)
    {
        $start = substr((string) $request->query('start'), 0, 10) ?: now()->startOfMonth()->toDateString();
        $end = substr((string) $request->query('end'), 0, 10) ?: now()->endOfMonth()->toDateString();

        $rows = Reservation::with(['facility:id,name', 'user:id,name'])
            ->whereDate('reservation_date', '>=', $start)
            ->whereDate('reservation_date', '<=', $end)
            ->when($request->query('facility_id'), fn ($q, $id) => $q->where('facility_id', $id))
            ->get();

        $colors = ['Approved' => '#1f5c4d', 'Pending' => '#b8860b', 'Declined' => '#b91c1c'];
        $events = [];
        $buckets = [];

        foreach ($rows as $r) {
            $date = Carbon::parse($r->reservation_date)->toDateString();
            $s = SlotRules::hhmmss($r->start_time);
            $e = SlotRules::hhmmss($r->end_time);
            $color = $colors[$r->status] ?? '#555555';

            $events[] = [
                'id' => (string) $r->id,
                'title' => ($r->facility->name ?? 'Facility') . ' · ' . ($r->user->name ?? 'User'),
                'start' => "{$date}T{$s}",
                'end' => "{$date}T{$e}",
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'type' => 'reservation',
                    'reservation_id' => $r->id,
                    'facility_id' => $r->facility_id,
                    'facility' => $r->facility->name ?? '',
                    'user_id' => $r->user_id,
                    'user_name' => $r->user->name ?? '',
                    'date' => $date,
                    'start' => substr($s, 0, 5),
                    'end' => substr($e, 0, 5),
                    'purpose' => $r->purpose,
                    'status' => $r->status,
                    'decline_message' => $r->decline_message,
                ],
            ];

            if ($r->status !== 'Declined') {
                $buckets["{$r->facility_id}|{$date}"]['name'] = $r->facility->name ?? 'Facility';
                $buckets["{$r->facility_id}|{$date}"]['rows'][] = [SlotRules::mins($r->start_time), SlotRules::mins($r->end_time)];
            }
        }

        // "Full / Occupied" na mga date
        foreach ($buckets as $key => $b) {
            if (SlotRules::isFullFromIntervals($b['rows'])) {
                [$facilityId, $date] = explode('|', $key);
                $events[] = [
                    'id' => "full-{$key}",
                    'title' => "FULL · {$b['name']}",
                    'start' => $date,
                    'allDay' => true,
                    'backgroundColor' => '#7f1d1d',
                    'borderColor' => '#7f1d1d',
                    'extendedProps' => ['type' => 'full', 'facility_id' => (int) $facilityId, 'date' => $date],
                ];
            }
        }

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        $user = $request->user();

        $r = new Reservation();
        $r->facility_id = $data['facility_id'];
        $r->user_id = $user->id;
        $r->reservation_date = $data['reservation_date'];
        $r->start_time = $data['start_time'];
        $r->end_time = $data['end_time'];
        $r->purpose = $data['purpose'] ?? '';
        // Staff/HOA = auto Approved (sila mismo ang nagreserve). Resident = Pending muna.
        $r->status = SlotRules::isStaff($user) ? 'Approved' : 'Pending';
        $r->save();

        return response()->json(['ok' => true, 'message' => 'Na-save ang reservation.']);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id) {
            abort(403, 'Hindi mo pwedeng i-edit ang reservation ng ibang tao. Approve o Decline lang ang pwede.');
        }
        if (! SlotRules::isStaff($user) && $reservation->status !== 'Pending') {
            abort(403, 'Hindi na pwedeng i-edit kapag na-review na ng HOA/Staff.');
        }

        $data = $this->validated($request, $reservation->id);

        $reservation->facility_id = $data['facility_id'];
        $reservation->reservation_date = $data['reservation_date'];
        $reservation->start_time = $data['start_time'];
        $reservation->end_time = $data['end_time'];
        $reservation->purpose = $data['purpose'] ?? '';
        $reservation->save();

        return response()->json(['ok' => true, 'message' => 'Na-update ang reservation.']);
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        if ($reservation->user_id !== $request->user()->id) {
            abort(403, 'Hindi mo pwedeng burahin ang reservation ng ibang tao.');
        }

        $reservation->delete();     

        return response()->json(['ok' => true, 'message' => 'Nabura ang reservation.']);
    }

    public function review(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if (! SlotRules::isStaff($user)) {
            abort(403, 'Para lang sa HOA/Staff ang Approve at Decline.');
        }
        if ($reservation->user_id === $user->id) {
            abort(403, 'Hindi mo pwedeng i-review ang sarili mong reservation.');
        }

        $data = $request->validate([
            'action' => 'required|in:approve,decline',
            'message' => 'nullable|string|max:500',
        ]);

        $message = trim((string) ($data['message'] ?? ''));

        if ($data['action'] === 'decline') {
            if ($message === '') {
                throw ValidationException::withMessages([
                    'message' => ['Mag-type muna ng message kung bakit idedecline bago ituloy.'],
                ]);
            }
            $reservation->status = 'Declined';
            $reservation->decline_message = $message;
            $chatBody = 'DECLINED ang reservation mo (' . $this->label($reservation) . '). Dahilan: ' . $message;
        } else {
            $date = Carbon::parse($reservation->reservation_date)->toDateString();
            if (SlotRules::conflict($reservation->facility_id, $date, $reservation->start_time, $reservation->end_time, $reservation->id)) {
                throw ValidationException::withMessages([
                    'action' => ['Hindi ma-approve: may ibang reservation na sa oras na ito.'],
                ]);
            }
            $reservation->status = 'Approved';
            $reservation->decline_message = null;
            $chatBody = 'APPROVED ang reservation mo (' . $this->label($reservation) . ').' . ($message !== '' ? ' Note: ' . $message : '');
        }

        $reservation->save();

        ReservationChatMessage::create([
            'resident_id' => $reservation->user_id,
            'sender_id' => $user->id,
            'body' => $chatBody,
        ]);

        $reservation->user->notify(new ReservationReviewed($reservation));

        return response()->json(['ok' => true, 'message' => 'Na-update ang status.']);
    }

    private function label(Reservation $r): string
    {
        $r->loadMissing('facility:id,name');
        $date = Carbon::parse($r->reservation_date)->format('M d, Y');

        return ($r->facility->name ?? 'Facility') . ', ' . $date . ' ' .
            SlotRules::pretty($r->start_time) . '-' . SlotRules::pretty($r->end_time);
    }

    private function validated(Request $request, ?int $ignoreId): array
    {
        $data = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'reservation_date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'purpose' => 'nullable|string|max:255',
        ], [
            'facility_id.required' => 'Pumili ng facility.',
            'reservation_date.required' => 'Pumili ng date.',
            'reservation_date.after_or_equal' => 'Hindi na pwedeng mag-reserve sa nakaraang date.',
            'start_time.required' => 'Ilagay ang start time.',
            'end_time.required' => 'Ilagay ang end time.',
        ]);

        $s = SlotRules::mins($data['start_time']);
        $e = SlotRules::mins($data['end_time']);

        if ($e <= $s) {
            throw ValidationException::withMessages(['end_time' => ['Dapat mas huli ang end time kaysa start time.']]);
        }
        if ($s < SlotRules::mins(SlotRules::OPEN) || $e > SlotRules::mins(SlotRules::CLOSE)) {
            throw ValidationException::withMessages(['start_time' => [
                'Bukas lang ang facility mula ' . SlotRules::pretty(SlotRules::OPEN) . ' hanggang ' . SlotRules::pretty(SlotRules::CLOSE) . '.',
            ]]);
        }

        if (SlotRules::isFull($data['facility_id'], $data['reservation_date'], $ignoreId)) {
            throw ValidationException::withMessages(['reservation_date' => [
                'OCCUPIED / FULL na ang date na ito. Wala nang available na oras. Pumili ng ibang date.',
            ]]);
        }

        $c = SlotRules::conflict($data['facility_id'], $data['reservation_date'], $data['start_time'], $data['end_time'], $ignoreId);
        if ($c) {
            throw ValidationException::withMessages(['start_time' => [
                'May nakasched na sa oras na iyon (' . SlotRules::pretty($c->start_time) . ' - ' . SlotRules::pretty($c->end_time) . '). Pumili ng ibang oras.',
            ]]);
        }

        return $data;
    }
}