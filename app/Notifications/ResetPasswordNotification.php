<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('بازیابی رمز عبور - حراج‌استون')
            ->greeting('سلام ' . $notifiable->name . '،')
            ->line('شما این ایمیل را دریافت کرده‌اید زیرا درخواست بازیابی رمز عبور برای حساب کاربری شما ثبت شده است.')
            ->action('تغییر رمز عبور', $url)
            ->line('این لینک بازیابی تا 60 دقیقه آینده معتبر است.')
            ->line('اگر شما درخواست بازیابی رمز عبور نداده‌اید، نیازی به انجام هیچ کاری نیست.')
            ->salutation('با تشکر، تیم حراج‌استون');
    }
}
