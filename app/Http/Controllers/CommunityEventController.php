<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use Illuminate\Http\Request;

class CommunityEventController extends Controller
{
    // ⚠️ PALITAN kung iba ang role names sa users table ninyo
    private function canManage(): bool
    {
        return in_array(auth()->user()->role, ['hoa', 'facility_staff']);
    }

    private function rules(): array
    {
        return [
            'title'       => 'required|string|max:150',
            'type'        => 'required|in:meeting,cleanup,seminar,barangay',
            'description' => 'nullable|string|max:1000',
            'location'    => 'nullable|string|max:150',
            'start_at'    => 'required|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
        ];
    }

    public function index()
    {
        return view('community-events.index', [
            'canManage' => $this->canManage(),
        ]);
    }

    // JSON para sa calendar
    public function feed()
    {
        return CommunityEvent::orderBy('start_at')->get()->map(fn ($e) => [
            'id'         => $e->id,
            'title'      => $e->title,
            'start'      => $e->start_at->format('Y-m-d\TH:i:s'),
            'end'        => $e->end_at?->format('Y-m-d\TH:i:s'),
            'classNames' => ['ev-' . $e->type],
            'extendedProps' => [
                'type'        => $e->type,
                'description' => $e->description,
                'location'    => $e->location,
            ],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($this->canManage(), 403);

        $data = $request->validate($this->rules());
        $data['created_by'] = auth()->id();

        return CommunityEvent::create($data);
    }

    public function update(Request $request, CommunityEvent $event)
    {
        abort_unless($this->canManage(), 403);

        $event->update($request->validate($this->rules()));
        return $event;
    }

    public function destroy(CommunityEvent $event)
    {
        abort_unless($this->canManage(), 403);

        $event->delete();
        return response()->noContent();
    }
}
