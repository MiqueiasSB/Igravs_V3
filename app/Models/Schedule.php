<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = ['service_id', 'person_id', 'role', 'status'];

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** @return BelongsTo<Person, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * @param  Builder<Schedule>  $query
     * @return Builder<Schedule>
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * @param  Builder<Schedule>  $query
     * @return Builder<Schedule>
     */
    public function scopeForService(Builder $query, int $serviceId): Builder
    {
        return $query->where('service_id', $serviceId);
    }
}
