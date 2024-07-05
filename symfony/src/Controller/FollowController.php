<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\FollowService;
use App\Services\Notification\NotificationFactory;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FollowController extends AbstractController
{
    private User $user;

    public function __construct(
        private FollowService $followService,
        private TokenStorageInterface $tokenStorage,
        private UserService $userService,
        private NotificationFactory $notificationFactory,
    ) {
        $this->followService = $followService;
        $this->tokenStorage = $tokenStorage;
        $this->user = $this->tokenStorage->getToken()->getUser();
        $this->userService = $userService;
        $this->notificationFactory = $notificationFactory;
    }

    #[Route('/api/follow/{userId}', name: 'follow', methods: ['POST'])]
    public function index(int $userId): JsonResponse
    {
        if (!$userId) {
            return new JsonResponse(['error' => 'userId is required'], 400);
        }

        if (!is_numeric($userId)) {
            return new JsonResponse(['error' => 'userId must be an integer'], 400);
        }

        if ($userId === $this->user->getId()) {
            return new JsonResponse(['error' => 'You cannot follow yourself'], 400);
        }

        $follow = $this->followService->follow($this->user->getId(), $userId);

        if ($follow instanceof JsonResponse) {
            return $follow;
        }

        $user = $this->userService->getUserFromId($userId);

        try {
            $notification = $this->notificationFactory->createNotification('email');
            $notification->sendNotification($user, 'follow');
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse([], 201);
    }
}
