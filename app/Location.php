<?php

namespace App;

use App\Listing;
use App\MapPosition;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public static function forListing(Listing $listing)
    {
        self::getNewCoords($listing);
    }

    protected static function getNewCoords($listing)
    {
        $geoCoder = new Geocoder($listing);
        $coords   = $geoCoder->getCoordinates($listing);

        Location::create([
            'listing_id' => $listing->id,
            'lat'        => $coords->lat,
            'long'       => $coords->long,
            'confidence' => $coords->confidence
        ]);
    }
}
