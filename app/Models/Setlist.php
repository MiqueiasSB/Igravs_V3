<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setlist extends Model
{
    protected $fillable = ['service_id', 'song', 'key', 'link', 'position'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
