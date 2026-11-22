<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationChatMessage extends Model
{
    protected $table = 'reservation_chat_messages';

    protected $fillable = [
        'resident_id', 'sender_id', 'body', 'read_at',
        'attachment_path', 'attachment_name', 'attachment_mime', 'attachment_size',
        'edited_at', 'deleted_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
