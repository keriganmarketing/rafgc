<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class ListingsSearchController extends Controller
{
    public function index(Request $request)
    {
        $omni         = $request->omni ?? '';
        $status       = $request->status ?? '';
        $area         = $request->area ?? '';
        $propertyType = isset($request->propertyType) && $request->propertyType !== 'Rental' ? $request->propertyType : '';
        $forclosure   = $request->forclosure ?? '';
        $minPrice     = $request->minPrice ?? '';
        $maxPrice     = $request->maxPrice ?? '';
        $beds         = $request->beds ?? '';
        $baths        = $request->baths ?? '';
        $sqft         = $request->sq_ft ?? '';
        $acreage      = $request->acreage ?? '';
        $waterfront   = $request->waterfront ?? '';
        $waterview    = $request->waterview ?? '';
        $sortBy       = $request->sortBy ?? 'date_modified';
        $orderBy      = $request->orderBy ?? 'DESC';

        if ($propertyType != '') {
            $propertyType = explode('|', $propertyType);
        }
        if ($status) {
            $status = explode('|', $status);
        }
        $listings = Listing::when($omni, function ($query) use ($omni) {
            $query->where(function ($query) use ($omni) {
                $query->whereRaw("city LIKE '%{$omni}%'")
                    ->orWhereRaw("zip LIKE '%{$omni}%'")
                    ->orWhereRaw("subdivision LIKE '%{$omni}%'")
                    ->orWhereRaw("full_address LIKE '%{$omni}%'")
                    ->orWhereRaw("mls_acct LIKE '%{$omni}%'");
            });
        })
            ->when($propertyType, function ($query) use ($propertyType) {
                return $query->whereIn('prop_type', $propertyType);
            })
            ->when($status, function ($query) use ($status) {
                return $query->whereIn('status', $status);
            })
            ->when($area, function ($query) use ($area) {
                return $query->where('area', $area)->orWhere('sub_area', $area);
            })
            ->when($minPrice, function ($query) use ($minPrice) {
                return $query->where('list_price', '>=', $minPrice);
            })
            ->when($maxPrice, function ($query) use ($maxPrice) {
                return $query->where('list_price', '<=', $maxPrice);
            })
            ->when($beds, function ($query) use ($beds) {
                return $query->where('bedrooms', '>=', $beds);
            })
            ->when($baths, function ($query) use ($baths) {
                return $query->where('baths', '>=', $baths);
            })
            ->when($sqft, function ($query) use ($sqft) {
                return $query->where('sqft_total', '>=', $sqft);
            })
            ->when($acreage, function ($query) use ($acreage) {
                return $query->where('acreage', '>=', $acreage);
            })
            ->when($waterfront, function ($query) use ($waterfront) {
                return $query->where('ftr_waterfront', '!=', null);
            })
            ->when($waterview, function ($query) use ($waterview) {
                return $query->where('ftr_waterview', '!=', null);
            })
            ->when($forclosure, function ($query) use ($forclosure) {
                return $query->where('ftr_ownership', 'like', '%Bankruptcy%')
                             ->orWhere('ftr_ownership', 'like','%Foreclosure%')
                             ->orWhere('ftr_ownership', 'like','%REO%');
            })
            ->orderBy($sortBy, $orderBy)
            ->paginate(36);

        // ProcessListingImpression::dispatch($listings);

        // returns paginated links (with GET variables intact!)
        $listings->appends($request->all())->links();

        return fractal($listings, new ListingTransformer)->toJson();
    }
}
