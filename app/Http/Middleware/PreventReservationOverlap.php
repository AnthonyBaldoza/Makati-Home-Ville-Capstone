<?php

namespace App\Http\Middleware;

use App\Support\ReservationSchedule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventReservationOverlap
{
    public function handle(Request $request, Closure $next): Response
    {
        $facility = $request->input('facility_id');
        $date     = $request->input('reservation_date');
        $start    = $request->input('start_time');
        $end      = $request->input('end_time');

        // Kulang ang input: ang validation ng controller ang bahala
        if (! $facility || ! $date || ! $start || ! $end) {
            return $next($request);
        }

        if (strtotime($end) <= strtotime($start)) {
            return back()->withInput()->withErrors([
                'end_time' => 'Dapat mas huli ang End time kaysa Start time.',
            ]);
        }

        // Oras ng bukas, FULL na ang araw, at overlap
        $error = ReservationSchedule::check($facility, $date, $start, $end);

        if ($error) {
            return back()->withInput()->withErrors(['start_time' => $error]);
        }

        return $next($request);
    }
}