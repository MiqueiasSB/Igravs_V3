<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['service_id', 'person_id', 'role', 'status'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
