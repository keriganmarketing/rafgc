<?php

namespace App;

use App\Jobs\ProcessImpression;
use Carbon\Carbon;
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

    public function impressions()
    {
        return $this->hasMany(Impression::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public static function featuredList($mlsNumbers)
    {
        $listings = Listing::whereIn('mls_acct', $mlsNumbers)->get();

        ProcessImpression::dispatch($listings);

        return fractal($listings, new ListingTransformer)->toJson();
    }

    public static function forAgent($agentCode)
    {
        $listings = Listing::where('la_code', $agentCode)->orWhere('co_la_code', $agentCode)->orWhere('sa_code', $agentCode)->get();

        ProcessImpression::dispatch($listings);

        return fractal($listings, new ListingTransformer);
    }

    public static function byMlsNumber($mlsNumber)
    {
        return Listing::where('mls_acct', $mlsNumber)->first();   
    }

    public function nuke()
    {
        $mediaObjects = MediaObject::where('listing_id', $this->id)->get();
        foreach ($mediaObjects as $MediaObject) {
            $MediaObject->delete();
        }
        $locations = Location::where('listing_id', $this->id)->get();
        foreach ($locations as $location) {
            $location->delete();
        }

        $clicks = Click::where('listing_id', $this->id)->get();
        foreach ($clicks as $click) {
            $click->delete();
        }

        $impressions = Impression::where('listing_id', $this->id)->get();
        foreach ($impressions as $impression) {
            $impression->delete();
        }

        $this->delete();
    }
    

    public function scopeRecentlySold($query, $days)
    {
        $days     = $days ?? 90;
        $now      = Carbon::now();
        $daysAgo  = $now->copy()->subDays($days);
        return $query->where('sold_date', '>=', $daysAgo);
    }

    public function scopeNewListings($query, $days)
    {
        $days     = $days ?? 10;
        $now      = Carbon::now();
        $daysAgo  = $now->copy()->subDays($days);
        return $query->where('list_date', '>=', $daysAgo);
    }

    public function scopeBy($query, $officeCode)
    {
        return $query->where('lo_code', $officeCode)
                     ->orWhere('co_lo_code', $officeCode)
                     ->orWhere('so_code', $officeCode);
    }

    public function scopeRecentlySoldBy($query, $officeCode)
    {
        $oneYearAgo = Carbon::now()->copy()->subYearNoOverflow();
        return $query->where('lo_code', $officeCode)
                     /* ->orWhere('co_lo_code', $officeCode) */
                     /* ->orWhere('so_code', $officeCode) */
                     ->where('sold_date', '>=', $oneYearAgo)
                     ->whereNotNull('sold_date');
    }

    public function scopeWaterFront($query)
    {
        return $query->where('ftr_waterfront', '!=', null);
    }

    public function scopeForclosures($query)
    {
        return $query->where('ftr_ownership', 'like', '%Bankruptcy%')
                     ->orWhere('ftr_ownership', 'like', '%Foreclosure%')
                     ->orWhere('ftr_ownership', 'like', '%REO%');
    }

    public function scopeContingentOrPending($query)
    {
        return $query->where('status', 'Contingent')->orWhere('status', 'Pending');
    }

    public function scopeExcludeAreas($query, $areas)
    {
        return $query->whereNotIn('area', $areas);

    }
}
