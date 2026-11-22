<?php

namespace App\Support;

use App\Models\Reservation;

class SlotRules
{
    // Oras ng bukas/sara ng facility. Palitan kung iba ang gusto nyo.
    public const OPEN = '06:00';
    public const CLOSE = '22:00';
    // Kapag wala nang bakanteng gap na kasing-haba nito, "Full" na ang date.
    public const MIN_SLOT_MINUTES = 30;

    public const STAFF_ROLES = ['HOA Admin', 'Facility Staff'];

    public static function isStaff($user): bool
    {
        return in_array($user->role->name ?? '', self::STAFF_ROLES, true);
    }

    public static function mins($time): int
    {
        $p = explode(':', (string) $time);
        return ((int) $p[0]) * 60 + (int) ($p[1] ?? 0);
    }

    public static function hhmmss($time): string
    {
        $p = explode(':', (string) $time);
        return sprintf('%02d:%02d:00', (int) $p[0], (int) ($p[1] ?? 0));
    }

    public static function pretty($time): string
    {
        $m = self::mins($time);
        $h = intdiv($m, 60);
        $i = $m % 60;
        $ampm = $h >= 12 ? 'PM' : 'AM';
        $h12 = $h % 12 === 0 ? 12 : $h % 12;
        return sprintf('%d:%02d %s', $h12, $i, $ampm);
    }

    public static function dayRows($facilityId, string $date, $ignoreId = null)
    {
        return Reservation::where('facility_id', $facilityId)
            ->whereDate('reservation_date', $date)
            ->where('status', '!=', 'Declined')
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->get();
    }

    public static function conflict($facilityId, string $date, $start, $end, $ignoreId = null): ?Reservation
    {
        $s = self::mins($start);
        $e = self::mins($end);

        foreach (self::dayRows($facilityId, $date, $ignoreId) as $r) {
            if (self::mins($r->start_time) < $e && self::mins($r->end_time) > $s) {
                return $r;
            }
        }
        return null;
    }

    /** $intervals = [[startMin, endMin], ...] */
    public static function isFullFromIntervals(array $intervals): bool
    {
        usort($intervals, fn ($a, $b) => $a[0] <=> $b[0]);

        $cursor = self::mins(self::OPEN);
        $close = self::mins(self::CLOSE);

        foreach ($intervals as [$s, $e]) {
            if ($s - $cursor >= self::MIN_SLOT_MINUTES) {
                return false;
            }
            $cursor = max($cursor, $e);
        }
        return ($close - $cursor) < self::MIN_SLOT_MINUTES;
    }

    public static function isFull($facilityId, string $date, $ignoreId = null): bool
    {
        $intervals = self::dayRows($facilityId, $date, $ignoreId)
            ->map(fn ($r) => [self::mins($r->start_time), self::mins($r->end_time)])
            ->all();

        return $intervals !== [] && self::isFullFromIntervals($intervals);
    }
}
