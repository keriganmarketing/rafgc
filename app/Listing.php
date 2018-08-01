<?php

namespace App;

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

    public static function featuredList($mlsNumbers)
    {
        return fractal(
            Listing::whereIn('mls_acct', $mlsNumbers)->get(),
            new ListingTransformer
        )->toJson();
    }

    public static function forAgent($agentCode)
    {
        return fractal(
            Listing::where('la_code', $agentCode)
                ->orWhere('co_la_code', $agentCode)
                ->orWhere('sa_code', $agentCode)
                ->get(),
            new ListingTransformer
        );
    }

    public function scopeRecentlySold($query, $days)
    {
        $days     = $days ?? 90;
        $now      = Carbon::now();
        $daysAgo  = $now->copy()->subDays($days);
        return $query->where('sold_date', '>=', $daysAgo);
    }
}
