<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Transformers\ListingTransformer;

class FeaturedListingsController extends Controller
{
    public function index(Request $request)
    {
        $mlsNumbers = explode('|', $request->mlsNumbers);

        return fractal(
            Listing::whereIn('mls_acct', $mlsNumbers)->where('status', 'Active')->get(),
            new ListingTransformer
        );
    }
}
