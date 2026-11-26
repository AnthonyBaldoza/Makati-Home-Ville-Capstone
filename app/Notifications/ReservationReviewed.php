<?php

namespace App\Notifications;

use App\Models\Reservation;
use App\Support\SlotRules;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class ReservationReviewed extends Notification
{
    public function __construct(public Reservation $reservation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $r = $this->reservation;
        $r->loadMissing('facility:id,name');

        $label = ($r->facility->name ?? 'Facility').', '
            .Carbon::parse($r->reservation_date)->format('M d, Y').' '
            .SlotRules::pretty($r->start_time).'-'.SlotRules::pretty($r->end_time);

        $message = $r->status === 'Approved'
            ? "Your reservation ({$label}) was approved."
            : "Your reservation ({$label}) was declined."
                .($r->decline_message ? " Reason: {$r->decline_message}" : '');

        return [
            'kind' => 'reservation',
            'reservation_id' => $r->id,
            'message' => $message,
            'url' => route('reservations.index', [], false),
        ];
    }
}