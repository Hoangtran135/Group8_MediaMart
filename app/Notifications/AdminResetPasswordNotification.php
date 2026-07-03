<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Yêu cầu đặt lại mật khẩu Admin - MediaMart')
            ->greeting('Xin chào ' . $notifiable->name . ',')
            ->line('Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản quản trị MediaMart.')
            ->action('Đặt lại mật khẩu', $url)
            ->line('Liên kết này sẽ hết hạn sau 60 phút.')
            ->line('Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này.');
    }
}
