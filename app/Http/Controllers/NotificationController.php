<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Newest first (the relationship is already ordered), latest 30 only
        $notifications = $request->user()->notifications()->limit(30)->get();

        return view('notifications.index', compact('notifications'));
    }

    public function read(Request $request, string $id)
    {
        // Looked up through the user's own notifications, so nobody can open another user's
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // The url is created by the server, but only follow it if it is a path inside this site
        $url = (string) ($notification->data['url'] ?? '');
        $isLocalPath = str_starts_with($url, '/') && ! str_starts_with($url, '//');

        return redirect($isLocalPath ? $url : route('notifications.index'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}