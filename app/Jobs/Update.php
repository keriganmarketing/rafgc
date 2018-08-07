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
use Illuminate\Notifications\Notifiable;

class Update implements ShouldQueue
{
    use Dispatchable, Notifiable,InteractsWithQueue, Queueable, SerializesModels;

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

        $updater->connect()->full();
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $admins = new User();

        $admins->notify(new FailedUpdate(json_encode($exception->getMessage())));
    }
}
