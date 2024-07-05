<?php

namespace App\Services\Notification;

use App\Entity\User;
use App\Interfaces\NotificationInterface;
use Symfony\Component\Mailer\MailerInterface;

final class NotificationFactory
{

    public function __construct(
        private MailerInterface $mailer
    ) {
        $this->mailer = $mailer;
    }

    public function createNotification(string $type): NotificationInterface
    {
        switch ($type) {
            case 'email':
                return new EmailNotification($this->mailer);
            case 'sms':
                return new SmsNotification();
            default:
                throw new \InvalidArgumentException('Invalid notification type');
        }
    }
}
