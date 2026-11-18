<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CommunityEvent extends Model
{
    protected $fillable = [
        'title', 'type', 'description', 'location',
        'start_at', 'end_at', 'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    /**
     * Hanapin ang event na sumasapaw sa oras na ito.
     * Overlap kapag: existing.start < bagong.end AT existing.end > bagong.start
     * (ang magkasunod na oras ay pwede)
     */
    public static function findConflict($start, $end, $ignoreId = null): ?self
    {
        $start = Carbon::parse($start);
        $end   = Carbon::parse($end);

        return static::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('start_at', '<', $end->format('Y-m-d H:i:s'))
            ->whereRaw('COALESCE(end_at, start_at) > ?', [$start->format('Y-m-d H:i:s')])
            ->first();
    }
}