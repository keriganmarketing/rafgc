<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class OurPropertiesController extends Controller
{
    public function index(Request $request, $officeCode)
    {
        $sort = isset($request->sort) && $request->sort !== '' ? explode('|', $request->sort) : [];
        $propertyType = $request->propertyType ?? null;
        $area = $request->area ?? null;
        $sortBy = (isset($sort[0]) && $sort[0] != null) ? $sort[0] : 'date_modified';
        $orderBy = (isset($sort[1]) && $sort[1] != null) ? $sort[1] : 'desc';

        $listings = Listing::where('lo_code', $officeCode)
                    ->orWhere('co_lo_code', $officeCode)
                    ->orWhere('so_code', $officeCode)
                    ->when($propertyType, function ($query) use ($propertyType) {
                        return $query->where('prop_type', $propertyType);
                    })
                    ->when($area, function ($query) use ($area) {
                        return $query->where('area', $area)->orWhere('sub_area', $area)->orWhere('city', $area);
                    })
                    ->orderBy($sortBy, $orderBy)
                    ->paginate(36);

        return fractal($listings, new ListingTransformer);
    }
}
