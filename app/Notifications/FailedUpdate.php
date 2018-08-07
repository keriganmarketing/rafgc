<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;

class FailedUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $errorMessage;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $message = $this->errorMessage;
        return (new SlackMessage)
                    ->from('RAFGC Robot', ':robot_face:')
                    ->to('#webdev')
                    ->content('Whoops! There was a failed updated on RAFGC!')
                    ->attachment(function ($attachment) use ($message){
                        $attachment->title('Error Message')
                                   ->content($message);
                    });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
