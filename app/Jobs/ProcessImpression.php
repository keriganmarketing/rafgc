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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::now()->copy()->toDateString();

        if ($impression = Impression::where('listing_id', $this->listing->id)->where('served_on', $today)->first()) {
            $impression->increment('counter');
        } else {
            Impression::create([
                'listing_id' => $this->listing->id,
                'served_on'  => $today,
                'counter' => 1
            ]);
        }
    }
}
