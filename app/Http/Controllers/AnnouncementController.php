<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $announcements = Announcement::with('user')->latest()->get(); # To avoid N+1 Query Problem

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules()); # Para hindi makapag-save ng kulang o maling data. 

        DB::transaction(function () use ($request, $validated) {
            $announcement = $request->user()->announcements() #Para automatic na ma-assign ang authenticated user bilang author
                ->create(Arr::only($validated, ['title', 'body'])); #ng announcement.

            $this->saveAttachments($request, $announcement);
        });

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement posted.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        $announcement->load(['user', 'attachments']);

        return view('announcements.show', compact('announcement')); #specified anouncement
    }

    public function edit(Announcement $announcement)
    {
        $announcement->load('attachments');

        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate(array_merge($this->rules(), [
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['integer'],
        ]));

        $pathsToDelete = [];

        DB::transaction(function () use ($request, $validated, $announcement, &$pathsToDelete) {
            $announcement->update(Arr::only($validated, ['title', 'body'])); #get new values

            // Only this announcement's own attachments can be removed
            $toRemove = $announcement->attachments()
                ->whereIn('id', $request->input('remove_media', []));

            $pathsToDelete = $toRemove->pluck('path')->all();
            $toRemove->delete();

            $this->saveAttachments($request, $announcement);
        });

        // Delete files only after the database changes succeeded
        Storage::disk('local')->delete($pathsToDelete);

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete(); // attachment rows are removed by ON DELETE CASCADE

        // The cascade does not touch the files, so remove the folder too
        Storage::disk('local')->deleteDirectory("announcements/{$announcement->id}");

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string'],
            'media' => ['nullable', 'array', 'max:5'],
            'media.*' => [
                'file',
                'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime',
                'max:20480', // 20 MB per file
            ],
        ];
    }

    private function saveAttachments(Request $request, Announcement $announcement): void
    {
        foreach ($request->file('media', []) as $file) {
            $announcement->attachments()->create([
                'path' => $file->store("announcements/{$announcement->id}", 'local'),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}