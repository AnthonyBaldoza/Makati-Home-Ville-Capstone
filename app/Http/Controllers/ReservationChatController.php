<?php

namespace App\Http\Controllers;

use App\Models\ReservationChatMessage;
use App\Models\User;
use App\Support\SlotRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationChatController extends Controller
{
    private const VOICE_MIMES = [
        'webm' => 'audio/webm', 'weba' => 'audio/webm',
        'ogg' => 'audio/ogg', 'oga' => 'audio/ogg', 'opus' => 'audio/ogg',
        'mp4' => 'audio/mp4', 'm4a' => 'audio/mp4',
        'aac' => 'audio/aac', 'mp3' => 'audio/mpeg', 'wav' => 'audio/wav',
    ];

    /** Listahan ng threads. Resident = isang thread (HOA/Staff). Staff = lahat ng residents. */
    public function threads()
    {
        $me = auth()->user();

        if (! SlotRules::isStaff($me)) {
            $last = ReservationChatMessage::where('resident_id', $me->id)->latest('id')->first();
            $unread = ReservationChatMessage::where('resident_id', $me->id)
                ->where('sender_id', '!=', $me->id)->whereNull('read_at')->count();

            return response()->json([
                'threads' => [[
                    'resident_id' => $me->id,
                    'name' => 'HOA / Staff',
                    'last' => $this->preview($last),
                    'unread' => $unread,
                ]],
                'total_unread' => $unread,
            ]);
        }

        $residents = User::whereHas('role', fn ($q) => $q->whereNotIn('name', SlotRules::STAFF_ROLES))
            ->orderBy('name')->get(['id', 'name']);

        $lastIds = ReservationChatMessage::selectRaw('resident_id, MAX(id) as last_id')
            ->groupBy('resident_id')->pluck('last_id', 'resident_id');
        $lastBodies = ReservationChatMessage::whereIn('id', $lastIds->values())->get()->keyBy('resident_id');
        $unreadMap = ReservationChatMessage::selectRaw('resident_id, COUNT(*) as c')
            ->whereColumn('sender_id', 'resident_id')->whereNull('read_at')
            ->groupBy('resident_id')->pluck('c', 'resident_id');

        $threads = $residents->map(fn ($u) => [
            'resident_id' => $u->id,
            'name' => $u->name,
            'last' => $this->preview($lastBodies[$u->id] ?? null),
            'last_id' => (int) ($lastIds[$u->id] ?? 0),
            'unread' => (int) ($unreadMap[$u->id] ?? 0),
        ])->sortByDesc('last_id')->values();

        return response()->json([
            'threads' => $threads,
            'total_unread' => (int) $threads->sum('unread'),
        ]);
    }

    public function messages(int $resident)
    {
        $me = auth()->user();
        $this->authorizeThread($me, $resident);

        ReservationChatMessage::where('resident_id', $resident)
            ->where('sender_id', '!=', $me->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = ReservationChatMessage::with('sender:id,name')
            ->where('resident_id', $resident)
            ->orderBy('id')->limit(300)->get()
            ->map(function ($m) use ($me) {
                $deleted = $m->deleted_at !== null;

                return [
                    'id' => $m->id,
                    'mine' => $m->sender_id === $me->id,
                    'sender' => $m->sender->name ?? 'User',
                    'body' => $deleted ? '' : $m->body,
                    'time' => $m->created_at?->format('M d, g:i A'),
                    'edited' => $m->edited_at !== null,
                    'deleted' => $deleted,
                    'v' => ($deleted ? 'd' : '') . ($m->edited_at?->timestamp ?? 0),
                    'attachment' => (! $deleted && $m->attachment_path) ? [
                        'kind' => $this->kind($m->attachment_mime),
                        'name' => $m->attachment_name,
                        'size' => $m->attachment_size,
                        'url' => url('/reservation-chat/file/' . $m->id),
                    ] : null,
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    public function send(Request $request, int $resident)
    {
        $me = auth()->user();
        $this->authorizeThread($me, $resident);

        $request->validate([
            'body' => 'nullable|string|max:1000',
            'voice' => 'nullable',
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm,weba,ogg,oga,opus,m4a,mp3,wav,aac,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
        ], [
            'file.uploaded' => 'Hindi na-upload ang file. Baka masyadong malaki (max 10 MB).',
            'file.max' => 'Masyadong malaki ang file. Hanggang 10 MB lang.',
            'file.mimes' => 'Hindi suportado ang uri ng file na ito.',
            'body.max' => 'Masyadong mahaba ang message (max 1000 characters).',
        ]);

        $body = trim((string) $request->input('body', ''));
        $file = $request->file('file');

        if ($body === '' && ! $file) {
            throw ValidationException::withMessages(['body' => ['Mag-type ng message o mag-attach ng file.']]);
        }

        $data = [
            'resident_id' => $resident,
            'sender_id' => $me->id,
            'body' => $body,
        ];

        if ($file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $mime = $file->getMimeType() ?: 'application/octet-stream';
            if ($request->boolean('voice')) {
                $mime = self::VOICE_MIMES[$ext] ?? 'audio/webm';
            }
            $name = preg_replace('/[^\w\s.\-()]+/u', '_', $file->getClientOriginalName());

            $data['attachment_path'] = $file->store('reservation-chat', 'local');
            $data['attachment_name'] = Str::limit($name ?: 'file', 120, '');
            $data['attachment_mime'] = $mime;
            $data['attachment_size'] = $file->getSize();
        }

        ReservationChatMessage::create($data);

        return response()->json(['ok' => true]);
    }

    /** I-edit ang sariling message. */
    public function update(Request $request, ReservationChatMessage $message)
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403, 'Sarili mo lang na message ang pwede mong i-edit.');
        }
        if ($message->deleted_at) {
            abort(422, 'Na-unsend na ang message na ito.');
        }

        $data = $request->validate(['body' => 'required|string|max:1000'], [
            'body.required' => 'Hindi pwedeng walang laman ang message.',
        ]);

        $message->body = trim($data['body']);
        $message->edited_at = now();
        $message->save();

        return response()->json(['ok' => true]);
    }

    /** I-unsend ang sariling message (pati attachment). */
    public function destroy(ReservationChatMessage $message)
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403, 'Sarili mo lang na message ang pwede mong i-unsend.');
        }

        if ($message->attachment_path) {
            Storage::disk('local')->delete($message->attachment_path);
        }

        $message->body = '';
        $message->attachment_path = null;
        $message->attachment_name = null;
        $message->attachment_mime = null;
        $message->attachment_size = null;
        $message->deleted_at = now();
        $message->save();

        return response()->json(['ok' => true]);
    }

    /** Ipakita/i-download ang attachment (para lang sa kasali sa usapan). */
    public function file(ReservationChatMessage $message)
    {
        $me = auth()->user();
        $this->authorizeThread($me, (int) $message->resident_id);

        if ($message->deleted_at || ! $message->attachment_path || ! Storage::disk('local')->exists($message->attachment_path)) {
            abort(404, 'Wala na ang file na ito.');
        }

        $mime = $message->attachment_mime ?: 'application/octet-stream';
        $inline = in_array($this->kind($mime), ['image', 'video', 'audio'], true) || $mime === 'application/pdf';
        $name = $message->attachment_name ?: 'file';

        $response = response()->file(Storage::disk('local')->path($message->attachment_path), [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $response->setContentDisposition($inline ? 'inline' : 'attachment', $name, Str::ascii($name) ?: 'file');

        return $response;
    }

    private function kind(?string $mime): string
    {
        $mime = (string) $mime;
        foreach (['image', 'video', 'audio'] as $k) {
            if (str_starts_with($mime, $k . '/')) {
                return $k;
            }
        }
        return 'file';
    }

    private function preview(?ReservationChatMessage $m): string
    {
        if (! $m) {
            return '';
        }
        if ($m->deleted_at) {
            return 'Na-unsend ang message';
        }
        if ($m->body !== '' && $m->body !== null) {
            return $m->body;
        }
        return match ($this->kind($m->attachment_mime)) {
            'audio' => '🎤 Voice message',
            'image' => '📷 Photo',
            'video' => '🎥 Video',
            default => '📎 ' . ($m->attachment_name ?: 'File'),
        };
    }

    private function authorizeThread($me, int $resident): void
    {
        if (! SlotRules::isStaff($me) && $me->id !== $resident) {
            abort(403, 'Hindi mo pwedeng buksan ang usapan ng ibang resident.');
        }
        if (! User::whereKey($resident)->exists()) {
            abort(404, 'Hindi nakita ang resident.');
        }
    }
}
