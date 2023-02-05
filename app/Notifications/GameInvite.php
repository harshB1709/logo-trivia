<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class GameInvite extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject(config('app.name') . ' - Access to Your Game')
                    ->greeting("Hello {$notifiable->display_name},")
                    ->line('Your game is now ready for you to play. To start playing, simply click on the link below.')
                    ->action('Game Link', URL::signedRoute('game', ['player' => $notifiable->id]))
                    ->line('We hope you enjoy the game and have a great time playing it! If you have any questions or concerns, please feel free to reach out to us.')
                    ->line(new HtmlString('<strong>Note: </strong>This link can be used only once. After you start the game, you can\'t reload or reuse the link.'));
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
