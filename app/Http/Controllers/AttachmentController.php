<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function show(Request $request, Attachment $attachment)
    {
        $user = $request->user();

        // Complaint attachments are private: owner or HOA Admin only.
        // Announcement attachments are visible to any logged-in user.
        if ($attachment->complaint_id) {
            $complaint = $attachment->complaint;

            if ($user->role->name !== 'HOA Admin' && $complaint->user_id !== $user->id) {
                abort(403);
            }
        }

        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        return response()->file(
            Storage::disk('local')->path($attachment->path),
            ['Content-Type' => $attachment->mime_type, 'X-Content-Type-Options' => 'nosniff']
        );
    }
}