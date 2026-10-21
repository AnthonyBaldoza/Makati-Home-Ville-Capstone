<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['facility_id', 'reservation_date', 'start_time', 'end_time', 'purpose'])]
class Reservation extends Model
{
    public const STATUSES = ['Pending', 'Approved', 'Declined'];

    public const BLOCKING_STATUSES = ['Approved'];

    protected function casts(): array
    {
        return ['reservation_date' => 'date'];
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApprovedOverlapping($query, $facilityId, $date, $start, $end)
    {
        return $query->where('facility_id', $facilityId)
            ->whereDate('reservation_date', $date)
            ->where('status', 'Approved')
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start);
    }
}
