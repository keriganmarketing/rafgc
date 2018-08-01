<?php

namespace App\Http\Controllers;

use App\Listing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class RecentlySoldController extends Controller
{
    public function index(Request $request)
    {
        $propertyType = $request->propertyType ?? null;
        $area = $request->area ?? null;
        $sortBy = (isset($request->sort) && $request->sort != null) ? explode('|', $request->sort)[0] : 'date_modified';
        $orderBy = (isset($request->sort) && $request->sort != null) ? explode('|', $request->sort)[1] : 'desc';

        $listings = Listing::recentlySold($request->days)
                    ->when($propertyType, function ($query) use ($propertyType) {
                        return $query->whereIn('prop_type', $propertyType);
                    })
                    ->when($area, function ($query) use ($area) {
                        return $query->where('area', $area)->orWhere('sub_area', $area)->orWhere('city', $area);
                    })
                    ->orderBy($sortBy, $orderBy)
                    ->paginate(36);

        return fractal($listings, new ListingTransformer);
    }
}
