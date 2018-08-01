<?php

namespace App\Http\Controllers;

use App\OmniTerm;
use Illuminate\Http\Request;

class OmniBarController extends Controller
{
    public function index()
    {
        return OmniTerm::all();
    }
}
