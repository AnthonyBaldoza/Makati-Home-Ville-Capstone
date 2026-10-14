<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Complaint::with('user')->latest();

        if ($user->role->name !== 'HOA Admin') {
            $query->where('user_id', $user->id);
        }

        $complaints = $query->get();

        return view('complaints.index', compact('complaints'));
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
        ]);

        $request->user()->complaints()->create($validated);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint submitted.');
    }

    public function show(Request $request, Complaint $complaint)
    {
        $user = $request->user();

        if ($user->role->name !== 'HOA Admin' && $complaint->user_id !== $user->id) {
            abort(403);
        }

        $complaint->load('user');

        return view('complaints.show', compact('complaint'));
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Resolved'])],
        ]);

        $complaint->status = $validated['status'];
        $complaint->save();

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Status updated.');
    }
}
