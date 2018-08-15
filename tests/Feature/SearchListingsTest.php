<?php

namespace Tests\Feature;

use App\Listing;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchListingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_listing_is_searchable_by_address()
    {
        $listing = factory(Listing::class)->create();
        // search for the listings address
        $this->searchFor(['omni', $listing->full_address]);
    }

    public function searchFor($column)
    {
        $key = $column[0];
        $value = $column[1];

        $listings =
        $listings = $this->get("/api/v1/search?{$column[0]}={$column[1]}");
        dd($listings);

        return $listings;
    }
}
