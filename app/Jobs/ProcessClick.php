<?php

namespace App\Jobs;

use App\Click;
use App\Listing;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessClick implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $listing;

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

        $clicksForToday = Click::where('listing_id', $this->listing->id)->where('clicked_on', $today)->first();
        if ($clicksForToday) {
            $clicksForToday->increment('counter');
        } else {
            click::create([
                'listing_id' => $this->listing->id,
                'clicked_on'  => $today,
                'counter' => 1
            ]);
        }
    }
}
