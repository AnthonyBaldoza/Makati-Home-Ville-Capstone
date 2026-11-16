<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['path', 'original_name', 'mime_type', 'size'])]
class Attachment extends Model
{
    public function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}