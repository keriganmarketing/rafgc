<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use Searchable;

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
