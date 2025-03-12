<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'date'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_event')
                    ->withPivot('isOrganizer')
                    ->withTimestamps();
    }

    public function organizers()
    {
        return $this->users()->wherePivot('isOrganizer', true);
    }
}
