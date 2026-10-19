<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Complaint;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $isAdmin = $user->role->name === 'HOA Admin';
        $announcements = Announcement::latest()->take(4)->get();
        $complaints = Complaint::with('user')
            ->visibleTo($user)
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact('isAdmin', 'announcements', 'complaints'));
    }
}
