<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UrlRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class FollowService
{


    public function __construct(
        private UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function follow(int $userId, int $followedUserId): JsonResponse|true
    {
        $user = $this->userRepository->getUserById($userId);
        $followedUser = $this->userRepository->getUserById($followedUserId);

        if (!$user || !$followedUser) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if ($this->userRepository->userFollowsUser($user, $followedUser)) {
            return new JsonResponse(['error' => 'User already follows this user'], 400);
        }

        if (!$this->userRepository->follow($user, $followedUser)) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return true;
    }
}
