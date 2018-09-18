<?php

namespace App\Jobs;

use App\User;
use App\Listing;
use App\Updater;
use Illuminate\Bus\Queueable;
use App\Notifications\FailedUpdate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;

class Update implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lastModified = Listing::pluck('date_modified')->max();
        $updater = new Updater($lastModified);

        try {
            $updater->connect()->full();
            BuildFullAddresses::dispatch();
        } catch (\Exception $e) {
            \Slack::send($e->getMessage());
        }
    }
}
