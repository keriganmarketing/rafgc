<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use App\Transformers\ListingTransformer;

class OurPropertiesController extends Controller
{
    public function index(Request $request, $officeCode)
    {
        return fractal(
            Listing::where('lo_code', $officeCode)
                ->orWhere('co_lo_code', $officeCode)
                ->get(),
            new ListingTransformer
        );
    }
}
