<?php

namespace App\Http\Controllers;

use App\Models\CommunityEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommunityEventController extends Controller
{
    // Sino ang pwedeng mag-add/edit/delete ng events
    private function canManage(): bool
    {
        $roleName = auth()->user()->role->name ?? '';

        return in_array($roleName, ['HOA Admin', 'Facility Staff'], true);
    }

    private function rules(): array
    {
        return [
            'title'       => 'required|string|max:150',
            'type'        => 'required|in:meeting,cleanup,seminar,barangay',
            'description' => 'nullable|string|max:1000',
            'location'    => 'nullable|string|max:150',
            'start_at'    => 'required|date',
            'end_at'      => 'required|date|after:start_at',
        ];
    }

    // Bawal ang overlapping na schedule
    private function assertNoOverlap(array $data, $ignoreId = null): void
    {
        $conflict = CommunityEvent::findConflict($data['start_at'], $data['end_at'], $ignoreId);

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_at' => sprintf(
                    'May schedule conflict: "%s" (%s - %s). Pumili ng ibang oras.',
                    $conflict->title,
                    $conflict->start_at->format('M j, g:i A'),
                    optional($conflict->end_at)->format('M j, g:i A') ?? 'no end time'
                ),
            ]);
        }
    }

    // Page ng calendar (lahat ng naka-login ay makakakita)
    public function index()
    {
        return view('community-events.index', [
            'canManage' => $this->canManage(),
        ]);
    }

    // JSON data para sa calendar
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

    // ADD: HOA Admin / Facility Staff lang
    public function store(Request $request)
    {
        abort_unless($this->canManage(), 403);

        $data = $request->validate($this->rules());
        $this->assertNoOverlap($data);

        $data['created_by'] = auth()->id();

        return CommunityEvent::create($data);
    }

    // EDIT: HOA Admin / Facility Staff lang
    public function update(Request $request, CommunityEvent $event)
    {
        abort_unless($this->canManage(), 403);

        $data = $request->validate($this->rules());
        $this->assertNoOverlap($data, $event->id); // huwag i-count ang sarili

        $event->update($data);

        return $event;
    }

    // DELETE: HOA Admin / Facility Staff lang
    public function destroy(CommunityEvent $event)
    {
        abort_unless($this->canManage(), 403);

        $event->delete();

        return response()->noContent();
    }
}