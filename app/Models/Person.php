<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'team_id', 'user_id', 'name', 'email', 'phone', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roles()
    {
        return $this->hasMany(PersonRole::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
