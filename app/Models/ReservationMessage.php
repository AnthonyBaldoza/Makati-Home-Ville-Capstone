<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationMessage extends Model
{
    protected $fillable = ['resident_id', 'sender_id', 'body'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
