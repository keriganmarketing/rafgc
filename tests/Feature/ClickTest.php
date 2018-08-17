<?php

namespace Tests\Feature;

use App\Listing;
use Tests\TestCase;
use App\Jobs\ProcessClick;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClickTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_click_is_registered_when_a_single_listing_is_served()
    {
        Queue::fake();

        $listing = create(Listing::class);

        $this->get('/api/v1/listing/'. $listing->mls_acct);

        Queue::assertPushed(ProcessClick::class, function ($job) use ($listing) {
            return $job->listing->id = $listing->id;
        });
    }
}
