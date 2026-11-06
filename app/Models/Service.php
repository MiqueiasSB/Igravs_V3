<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
        protected $fillable = ['team_id', 'date', 'time', 'type'];

    protected $casts = [
        'date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function setlists()
    {
        return $this->hasMany(Setlist::class)->orderBy('position');
    }
}
