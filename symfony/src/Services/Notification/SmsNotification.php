<?php

namespace App\Services\Notification;

use App\Entity\User;
use App\Interfaces\NotificationInterface;

class SmsNotification implements NotificationInterface
{
    public function sendNotification(User $userTo, string $type, string $url = null): void
    {
        throw new \Exception('Method not implemented');
    }
}
