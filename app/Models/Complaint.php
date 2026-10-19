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

    public function scopeVisibleTo($query, User $user)
    {
        return $user->role->name === 'HOA Admin' ? $query : $query->where('user_id', $user->id);
    }

    public function statusClass(): string
    {
        return [
            'Pending' => 's-pending',
            'In Progress' => 's-progress',
            'Resolved' => 's-done',
        ][$this->status] ?? '';
    }
}
