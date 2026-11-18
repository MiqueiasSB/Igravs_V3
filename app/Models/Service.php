<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['team_id', 'date', 'time', 'type'];

    protected $casts = [
        'date' => 'date',
    ];

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<Schedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /** @return HasMany<Setlist, $this> */
    public function setlists(): HasMany
    {
        return $this->hasMany(Setlist::class);
    }
}
