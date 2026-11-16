<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::with('user') #To show the complaint in order with the latest first and with the name of the user
            ->visibleTo($request->user())
            ->latest()
            ->get();

        return view('complaints.index', compact('complaints')); # return the complaints view 
    }

    public function create()
    {
        return view('complaints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'media' => ['nullable', 'array', 'max:5'],
            'media.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime',
                'max:20480', // 20 MB per file
            ],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $complaint = $request->user()->complaints()
                ->create(Arr::only($validated, ['title', 'description']));

            foreach ($request->file('media', []) as $file) {
                $complaint->attachments()->create([
                    'path' => $file->store("complaints/{$complaint->id}", 'local'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        });

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint submitted.');
    }

    public function show(Request $request, Complaint $complaint)
    {
        $user = $request->user();

        if ($user->role->name !== 'HOA Admin' && $complaint->user_id !== $user->id) {
            abort(403);
        }

        $complaint->load(['user', 'attachments']);

        return view('complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Complaint::STATUSES)],
        ]);

        $complaint->status = $validated['status'];
        $complaint->save();

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Status updated.');
    }
}