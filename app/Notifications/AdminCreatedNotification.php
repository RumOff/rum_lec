<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;

class AdminCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $loginUrl = config('app.url') . '/admin/login';

        return (new MailMessage)
            ->subject('[重要] 管理者アカウント登録完了のお知らせ')
            ->greeting($notifiable->name . '様')
            ->priority(1)
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()
                    ->addTextHeader('X-Priority', '1')
                    ->addTextHeader('Importance', 'high')
                    ->addTextHeader('X-MSMail-Priority', 'High')
                    ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
                    ->addTextHeader('Auto-Submitted', 'auto-generated')
                    ->addTextHeader('X-PM-Message-Type', 'system');
            })
            ->line('新しい管理者アカウントが作成されました。以下の情報でログインしてください。')
            ->line('メールアドレス: ' . $notifiable->email)
            ->line('初期パスワード: ' . $this->password)
            ->action('管理画面にログイン', $loginUrl)
            ->line('セキュリティ保持のため、初回ログイン後にパスワードの変更を必ず行ってください。')
            ->line('本メールに心当たりがない場合は、お手数ですが破棄してください。');
    }
}
