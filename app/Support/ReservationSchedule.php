<?php

namespace App\Support;

use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationSchedule
{
    public const COL = [
        'facility' => 'facility_id',
        'date'     => 'reservation_date',
        'start'    => 'start_time',
        'end'      => 'end_time',
        'purpose'  => 'purpose',
        'status'   => 'status',
    ];

    // ⚠️ Pwede mong baguhin: oras ng bukas ng facilities at minimum na slot
    public const OPEN     = '06:00';
    public const CLOSE    = '22:00';
    public const MIN_SLOT = 60; // minuto

    // Hindi na hawak ang slot kapag ganito ang status
    public const IGNORED = ['rejected', 'cancelled', 'canceled', 'declined'];

    public static function facilityName(Reservation $r): string
    {
        $f = $r->facility;

        return (string) ($f->name ?? $f->facility_name ?? $f->title ?? ('Facility #' . $r->facility_id));
    }

    // Pending + Approved lang ang may hawak sa slot
    public static function active($query)
    {
        $col = self::COL['status'];
        $in  = implode(',', array_fill(0, count(self::IGNORED), '?'));

        return $query->whereRaw("LOWER({$col}) NOT IN ({$in})", self::IGNORED);
    }

    public static function hoursText(): string
    {
        return Carbon::parse(self::OPEN)->format('g:i A') . ' - ' . Carbon::parse(self::CLOSE)->format('g:i A');
    }

    public static function outsideHours($start, $end): bool
    {
        $s = Carbon::parse($start)->format('H:i');
        $e = Carbon::parse($end)->format('H:i');

        return $s < self::OPEN || $e > self::CLOSE;
    }

    // Hanapin ang reservation na sumasapaw (parehong facility, parehong araw)
    public static function findConflict($facilityId, $date, $start, $end, $ignoreId = null): ?Reservation
    {
        $c     = self::COL;
        $date  = Carbon::parse($date)->format('Y-m-d');
        $start = Carbon::parse($start)->format('H:i:s');
        $end   = Carbon::parse($end)->format('H:i:s');

        return self::active(Reservation::query())
            ->with('facility')
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where($c['facility'], $facilityId)
            ->whereDate($c['date'], $date)
            ->where($c['start'], '<', $end)
            ->where($c['end'], '>', $start)
            ->first();
    }

    // Libreng oras sa araw na iyon: [['06:00','08:00'], ['12:00','22:00']]
    public static function freeSlots($facilityId, $date, $ignoreId = null): array
    {
        $c = self::COL;

        $rows = self::active(Reservation::query())
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where($c['facility'], $facilityId)
            ->whereDate($c['date'], Carbon::parse($date)->format('Y-m-d'))
            ->orderBy($c['start'])
            ->get();

        $cursor = self::OPEN;
        $free   = [];

        foreach ($rows as $r) {
            $s = Carbon::parse($r->{$c['start']})->format('H:i');
            $e = Carbon::parse($r->{$c['end']})->format('H:i');

            if ($s > $cursor) {
                $free[] = [$cursor, $s];
            }
            if ($e > $cursor) {
                $cursor = $e;
            }
        }

        if ($cursor < self::CLOSE) {
            $free[] = [$cursor, self::CLOSE];
        }

        // Itira lang ang gap na kasya sa minimum slot
        return array_values(array_filter(
            $free,
            fn ($g) => (strtotime($g[1]) - strtotime($g[0])) / 60 >= self::MIN_SLOT
        ));
    }

    public static function freeText(array $slots): string
    {
        return implode(', ', array_map(
            fn ($g) => Carbon::parse($g[0])->format('g:i A') . ' - ' . Carbon::parse($g[1])->format('g:i A'),
            $slots
        ));
    }

    /**
     * Isang check para sa lahat: oras ng bukas, FULL na ba ang araw, at overlap.
     * Ibinabalik ang error message, o null kung pwede.
     */
    public static function check($facilityId, $date, $start, $end, $ignoreId = null): ?string
    {
        if (self::outsideHours($start, $end)) {
            return 'Bukas lang ang facility mula ' . self::hoursText() . '. Pumili ng oras sa loob nito.';
        }

        $name = optional(Facility::find($facilityId))->name ?? 'Facility';
        $day  = Carbon::parse($date)->format('l, M j, Y');
        $free = self::freeSlots($facilityId, $date, $ignoreId);

        if (! $free) {
            return "OCCUPIED / FULL na ang {$name} sa {$day}. Wala nang available na oras. Pumili ng ibang date.";
        }

        $conflict = self::findConflict($facilityId, $date, $start, $end, $ignoreId);

        if ($conflict) {
            return sprintf(
                'May nakaschedule na sa oras na iyon: %s ay naka-reserve %s - %s. Libre pa sa araw na ito: %s.',
                $name,
                Carbon::parse($conflict->{self::COL['start']})->format('g:i A'),
                Carbon::parse($conflict->{self::COL['end']})->format('g:i A'),
                self::freeText($free)
            );
        }

        return null;
    }

    // I-convert ang reservation papunta sa format ng calendar
    public static function toEvent(Reservation $r): array
    {
        $c   = self::COL;
        $day = Carbon::parse($r->{$c['date']})->format('Y-m-d');

        $start = Carbon::parse($day . ' ' . Carbon::parse($r->{$c['start']})->format('H:i:s'));
        $end   = Carbon::parse($day . ' ' . Carbon::parse($r->{$c['end']})->format('H:i:s'));

        $status   = strtolower((string) $r->{$c['status']});
        $declined = in_array($status, self::IGNORED, true);
        $approved = in_array($status, ['approved', 'confirmed'], true);

        if ($declined) {
            [$key, $label] = ['declined', 'Declined'];
        } elseif ($approved && now()->between($start, $end)) {
            [$key, $label] = ['occupied', 'Occupied now'];
        } elseif ($approved) {
            [$key, $label] = ['reserved', 'Reserved'];
        } else {
            [$key, $label] = ['pending', 'Pending approval'];
        }

        $who      = $r->user->name ?? null;
        $facility = self::facilityName($r);

        return [
            'id'         => $r->id,
            'title'      => $facility . ($who ? ' · ' . $who : ''),
            'start'      => $start->format('Y-m-d\TH:i:s'),
            'end'        => $end->format('Y-m-d\TH:i:s'),
            'classNames' => ['rs-' . $key],
            'extendedProps' => [
                'kind'          => 'reservation',
                'facility'      => $facility,
                'facility_id'   => $r->facility_id,
                'status'        => $label,
                'status_raw'    => $r->{$c['status']},
                'purpose'       => $r->{$c['purpose']} ?? '',
                'who'           => $who,
                'mine'          => (int) $r->user_id === (int) auth()->id(),
                'date'          => $day,
                'start_hm'      => $start->format('H:i'),
                'end_hm'        => $end->format('H:i'),
                'staff_message' => $r->staff_message,
            ],
        ];
    }
}