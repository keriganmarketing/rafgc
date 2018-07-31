<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $guarded = [];
    protected $with    = ['mediaObjects', 'location'];

    public function mediaObjects()
    {
        return $this->hasMany(MediaObject::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class);
    }
}
