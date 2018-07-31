<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ListingTransformer;

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

    public static function featuredList(Request $request)
    {
        $mlsNumbers = explode('|', $request->mlsNumbers);

        return fractal(
            Listing::whereIn('mls_acct', $mlsNumbers)->get(),
            new ListingTransformer
        )->toJson();
    }
}
