<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $isResident = $user->role->name === 'Resident';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'block' => [Rule::requiredIf($isResident), 'nullable', 'string', 'max:50'],
            'lot' => [Rule::requiredIf($isResident), 'nullable', 'string', 'max:50'],
            'mobile_number' => [
                'required',
                'regex:/^09\d{9}$/',
                Rule::unique('users', 'mobile_number')->ignore($user->id),
            ],
            'avatar' => [
                'nullable',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp', // checks the file content, not just the extension
                'max:2048', // 2 MB
                'dimensions:min_width=100,min_height=100,max_width=6000,max_height=6000',
            ],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        // Only these four fields are editable. Email and role are never taken from the form.
        $user->fill(Arr::only($validated, ['name', 'block', 'lot', 'mobile_number']));

        $oldPath = $user->avatar_path;
        $newPath = null;

        if ($request->hasFile('avatar')) {
            // store() picks a random file name, so the client's file name is never used
            $newPath = $request->file('avatar')->store("avatars/{$user->id}", 'local');
            $user->avatar_path = $newPath;
        } elseif ($request->boolean('remove_avatar')) {
            $user->avatar_path = null;
        }

        try {
            $user->save();
        } catch (\Throwable $e) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath); // do not leave an orphan file behind
            }

            throw $e;
        }

        // Delete the old file only after the database change succeeded
        if ($oldPath && $oldPath !== $user->avatar_path) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }

    public function avatar(User $user)
    {
        abort_unless($user->avatar_path && Storage::disk('local')->exists($user->avatar_path), 404);

        return response()->file(Storage::disk('local')->path($user->avatar_path), [
            'Content-Type' => Storage::disk('local')->mimeType($user->avatar_path),
            'Cache-Control' => 'private, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}