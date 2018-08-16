<?php

namespace Tests\Feature;

use App\Jobs\ProcessImpression;
use App\Listing;
use App\Impression;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImpressionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_impression_is_registered_when_a_listing_is_served()
    {
        Queue::fake();

        $listing = create(Listing::class);

        $results = $this->searchFor(['mls_acct', $listing->mls_acct]);

        Queue::assertPushed(ProcessImpression::class, function ($job) use ($listing) {
            return $job->listings[0]->id = $listing->id;
        });
    }

    public function searchFor($column)
    {
        $key = $column[0];
        $value = $column[1];

        $listings = $this->get("/api/v1/search?{$column[0]}={$column[1]}");

        return $listings;
    }
}
