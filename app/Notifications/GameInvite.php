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
        $game_invite_validity = config('app.invite_validity_mins');
        $notifiable->invite_expires_at = now()->addMinutes($game_invite_validity);
        $notifiable->save();

        return (new MailMessage)
                    ->subject("Tech Pictionary @ LaraconIN 2023 | Access your game")
                    ->greeting("Hello {$notifiable->display_name},")
                    ->line('Your game is now ready for you to play. To start playing, simply click on the link below.')
                    ->action('Game Link', URL::signedRoute('game', ['player' => $notifiable->id]))
                    ->line('We hope you enjoy the game and have a great time playing it! If you have any questions or concerns, please feel free to reach out to us at our stall B6.')
                    ->line(new HtmlString("<strong>Note: </strong>This link can be used only once and will expire after {$game_invite_validity} mins. Please open the link as soon as you receive it. After you start the game, you can\'t reload or reuse the link."))
                    ->line("Best of luck! And we hope you win the iPhone 14 @ LaraconIN 2023");
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
