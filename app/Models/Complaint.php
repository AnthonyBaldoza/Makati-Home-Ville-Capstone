<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description'])]
class Complaint extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
