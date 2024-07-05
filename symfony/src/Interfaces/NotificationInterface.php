<?php

namespace App\Interfaces;

use App\Entity\User;

interface NotificationInterface
{
    public function sendNotification(User $userTo, string $type, string $url = null): void;
}
