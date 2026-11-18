<?php

namespace App\Models;

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
}
