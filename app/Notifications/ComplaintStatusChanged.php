<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Notifications\Notification;

class ComplaintStatusChanged extends Notification
{
    public function __construct(public Complaint $complaint)
    {
    }

    /**
     * Today the notification is stored in the database (the bell).
     * To also send an email or an SMS later, add 'mail' or an SMS channel here.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'kind' => 'complaint',
            'complaint_id' => $this->complaint->id,
            'message' => "Your complaint \"{$this->complaint->title}\" is now {$this->complaint->status}.",
            'url' => route('complaints.show', $this->complaint, false),
        ];
    }
}