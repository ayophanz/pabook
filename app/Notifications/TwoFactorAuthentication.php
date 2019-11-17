<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TwoFactorAuthentication extends Notification
{
    use Queueable;
    protected $VerifyCode;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($VerifyCode)
    {
        $this->VerifyCode = $VerifyCode;
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
        $ToName = $notifiable->name;

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject(Lang::get(config('app.name').' | Two Factor Authentication'))
            ->line(Lang::get('You are receiving this email because our system trying to ensure that you are the owner.'))
            ->action(Lang::get('Your Token: '.$this->VerifyCode),  '#')
            ->line(Lang::get('Please copy the token and paste to '.config('app.name').' token field.'))
            ->greeting(Lang::get('Hello! '.$ToName.', '));

    }

}
