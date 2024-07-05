<?php

namespace App\Services\Notification;

use App\Entity\User;
use App\Interfaces\NotificationInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotification implements NotificationInterface
{
    public function __construct(
        private MailerInterface $mailer
    ) {
        $this->mailer = $mailer;
    }

    public function sendNotification(User $userTo, string $type, string $url = null): void
    {
        $email = (new Email())
            ->from('pippo@pluto.it')
            ->to($userTo->getEmail());

        switch ($type) {
            case 'like':
                $email->subject('New like on your post!');
                $email->text('Someone liked your post: ' . $url);
                break;
            case 'follow':
                $email->subject('New follower!');
                $email->text('Someone followed you!');
                break;
            case 'new_url':
                $email->subject('New URL shared!');
                $email->text('Someone shared a new URL: ' . $url);
                break;
            default:
                throw new \InvalidArgumentException('Invalid notification type: ' . $type);
        }

        $this->mailer->send($email);
    }
}
