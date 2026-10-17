<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description'])]
class Complaint extends Model
{
    public const STATUSES = ['Pending', 'In Progress', 'Resolved'];

    public function user()
    {
        return $this->belongsTo(User::class);

    }
}
