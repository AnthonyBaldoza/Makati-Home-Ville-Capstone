<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Complaint;
use App\Models\Reservation;
use App\Models\User;
use App\Support\SlotRules;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return view('welcome');
        }

        $isAdmin = $user->role->name === 'HOA Admin';

        // HOA Admin and Facility Staff get the sidebar dashboard
        if (SlotRules::isStaff($user)) {
            return $this->staffDashboard($user, $isAdmin);
        }

        $announcements = Announcement::latest()->take(4)->get();
        $complaints = Complaint::with('user')
            ->visibleTo($user)
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact('isAdmin', 'announcements', 'complaints'));
    }

    private function staffDashboard(User $user, bool $isAdmin)
    {
        $announcements = Announcement::latest()->take(4)->get();

        // Reservations: one grouped query for the counts, plus the next few waiting for review
        $reservationCounts = Reservation::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingReservations = Reservation::with(['facility:id,name', 'user:id,name'])
            ->where('status', 'Pending')
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $upcomingApproved = Reservation::where('status', 'Approved')
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->count();

        $stats = [];

        // Only the HOA Admin sees every complaint, so the complaint numbers are Admin-only
        $complaintCounts = collect();
        $complaints = collect();
        if ($isAdmin) {
            $complaintCounts = Complaint::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $complaints = Complaint::with('user')->latest()->take(5)->get();

            $stats[] = [
                'label' => 'Pending complaints',
                'value' => (int) ($complaintCounts['Pending'] ?? 0),
                'hint' => (int) ($complaintCounts['In Progress'] ?? 0).' in progress',
                'icon' => 'alert',
                'href' => route('complaints.index'),
                'warn' => ($complaintCounts['Pending'] ?? 0) > 0,
            ];
        }

        $stats[] = [
            'label' => 'Reservations to review',
            'value' => (int) ($reservationCounts['Pending'] ?? 0),
            'hint' => 'waiting for approval',
            'icon' => 'clock',
            'href' => route('reservations.calendar'),
            'warn' => ($reservationCounts['Pending'] ?? 0) > 0,
        ];
        $stats[] = [
            'label' => 'Upcoming reservations',
            'value' => $upcomingApproved,
            'hint' => 'approved, from today on',
            'icon' => 'calendar',
            'href' => route('reservations.calendar'),
            'warn' => false,
        ];

        if ($isAdmin) {
            $stats[] = [
                'label' => 'Residents',
                'value' => User::whereHas('role', fn ($q) => $q->where('name', 'Resident'))->count(),
                'hint' => 'registered accounts',
                'icon' => 'users',
                'href' => null,
                'warn' => false,
            ];
        }

        $stats[] = [
            'label' => 'Announcements',
            'value' => Announcement::count(),
            'hint' => Announcement::where('created_at', '>=', now()->startOfMonth())->count().' this month',
            'icon' => 'megaphone',
            'href' => route('announcements.index'),
            'warn' => false,
        ];

        return view('home-staff', compact(
            'isAdmin', 'stats', 'announcements', 'complaints', 'complaintCounts', 'reservationCounts', 'pendingReservations'
        ));
    }
}