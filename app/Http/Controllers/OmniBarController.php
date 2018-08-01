<?php

namespace App\Http\Controllers;

use App\OmniTerm;
use Illuminate\Http\Request;

class OmniBarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        return OmniTerm::where('value', 'like', "'{$search}%'")->distinct()->get();
    }
}
