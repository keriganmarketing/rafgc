<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Updater;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FailedUpdate;
use App\User;

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
        $updater = new Updater;

        try {
            $updater->connect()->full();
            BuildFullAddresses::dispatch();
        } catch (\Exception $e) {
            \Slack::send($e->getMessage());
        }
    }
}
