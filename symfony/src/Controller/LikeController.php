<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\LikeService;
use App\Services\Notification\NotificationFactory;
use App\Services\UrlService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LikeController extends AbstractController
{
    private User $user;
    public function __construct(
        private LikeService $likeService,
        private NotificationFactory $notificationFactory,
        private UserService $userService,
        private UrlService $urlService,
        private TokenStorageInterface $tokenStorageInterface
    ) {
        $this->likeService = $likeService;
        $this->notificationFactory = $notificationFactory;
        $this->userService = $userService;
        $this->user = $this->tokenStorageInterface->getToken()->getUser();
    }

    #[Route('/api/like/{urlId}', name: 'like', methods: ['POST'])]
    public function like(int $urlId): JsonResponse
    {
        if (!$urlId) {
            return new JsonResponse(['error' => 'url_id is required'], 400);
        }

        if (!is_numeric($urlId)) {
            return new JsonResponse(['error' => 'url_id must be an integer'], 400);
        }


        $likeResponse = $this->likeService->likeUrl($this->user->getId(), $urlId);

        if ($likeResponse instanceof JsonResponse) {
            return $likeResponse;
        }

        $userUrl = $this->urlService->getUserUrl($urlId);
        $user = $this->userService->getUserFromId($userUrl[0]['id']);
        try {
            $notification = $this->notificationFactory->createNotification('email');
            $notification->sendNotification($user, 'like', $likeResponse->getUrl());
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse([], 201);
    }
}
