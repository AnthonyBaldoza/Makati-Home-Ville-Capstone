<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description'])]
class Facility extends Model
{
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
