<?php

namespace App\Notifications;

use App\Models\Survey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;

class UserSurveyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $emailSubject;
    protected string $emailBody;

    public function __construct(string $emailSubject, string $emailBody)
    {
        $this->emailSubject = $emailSubject;
        $this->emailBody = $emailBody;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $emailBody = $this->extractSurveyLinkAndSplitBody($this->emailBody);
        $mailMessage = (new MailMessage)
            ->subject($this->emailSubject);
        if ($notifiable->name) {
            $mailMessage->greeting($notifiable->name . ' 様');
        }
        $mailMessage->priority(1)
            ->withSymfonyMessage(function (Email $message) {
                $message->getHeaders()
                    ->addTextHeader('X-Priority', '1')
                    ->addTextHeader('Importance', 'high')
                    ->addTextHeader('X-MSMail-Priority', 'High')
                    ->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
                    ->addTextHeader('Auto-Submitted', 'auto-generated')
                    ->addTextHeader('X-PM-Message-Type', 'system');
            });
        // 上部の本文を追加
        foreach (explode("\n", $emailBody[0]) as $line) {
            $mailMessage->line($line);
        }
        // リンク部分をactionで挿入
        if (!empty($emailBody[1])) {
            $mailMessage->action('アンケートに回答する', $emailBody[1]);
        }
        // 下部の本文を追加
        foreach (explode("\n", $emailBody[2]) as $line) {
            $mailMessage->line($line);
        }
        logger()->info('Mail Message Content', [
            'mailMessage' => $mailMessage,
        ]);
        return $mailMessage;
    }

    /**
     * 本文を上部・リンク・下部に分割するメソッド
     */
    protected function extractSurveyLinkAndSplitBody(string $body): array
    {
        $link = '';
        $bodyBeforeLink = $body;
        $bodyAfterLink = '';
        if (preg_match('/\[アンケートリンク:(.*?)\]/', $body, $matches)) {
            $link = trim($matches[1]);
            // リンク部分を削除し、上部と下部に分割
            $parts = preg_split('/\[アンケートリンク:.*?\]/', $body);
            $bodyBeforeLink = $parts[0] ?? '';
            $bodyAfterLink = $parts[1] ?? '';
        }
        return [$bodyBeforeLink, $link, $bodyAfterLink];
    }
}
