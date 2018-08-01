<?php
namespace App;

use App\Listing;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class ScopedSearch
{
    protected $request;
    protected $customScope;
    protected $args;
    protected $filters;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->filters = new SearchFilters($request);
    }

    public function setScope($scopedMethod, $args = [])
    {
        $this->customScope = $scopedMethod;
        $this->args = $args;

        return $this;
    }

    public function get()
    {
        $listing = new Listing();
        $filters = $this->filters;
        $listings = $listing->__call($this->customScope, $this->args)
                    ->when($filters->propertyType, function ($query) use ($filters) {
                        return $query->where('prop_type', $filters->propertyType);
                    })
                    ->when($filters->area, function ($query) use ($filters) {
                        $f = $filters;
                        return $query->where(function ($q) use ($f){
                            return $q->where('area', $f->area)
                                     ->orWhere('sub_area', $f->area)
                                     ->orWhere('city', $f->area)
                                     ->orWhere('subdivision', $f->area);
                        });
                    })
                    ->orderBy($filters->sortBy, $filters->orderBy)
                    ->paginate(36);

        return fractal($listings, new ListingTransformer);
    }
}
