<?php

namespace App\Jobs;

use App\Listing;
use Carbon\Carbon;
use App\Impression;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessImpression implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $listings;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listings)
    {
        $this->listings = $listings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::now()->copy()->toDateString();

        foreach ($this->listings as $listing) {
            $impressionsForToday = Impression::where('listing_id', $listing->id)->where('served_on', $today)->first();
            if ($impressionsForToday) {
                $impressionsForToday->increment('counter');
            } else {
                Impression::create([
                    'listing_id' => $listing->id,
                    'served_on'  => $today,
                    'counter' => 1
                ]);
            }
        }
    }
}
