<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $guarded = [];
    protected $with    = ['mediaObjects'];

    public function mediaObjects()
    {
        return $this->hasMany(MediaObject::class);
    }
}
