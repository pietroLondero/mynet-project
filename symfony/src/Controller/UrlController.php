<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Notification\NotificationFactory;
use App\Services\UrlService;
use App\Services\UserService;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UrlController extends AbstractController
{
    private User $user;
    public function __construct(
        private UrlService $urlService,
        private TokenStorageInterface $tokenStorage,
        private UserService $userService,
        private NotificationFactory $notificationFactory
    ) {
        $this->urlService = $urlService;
        $this->user = $this->tokenStorage->getToken()->getUser();
        $this->userService = $userService;
        $this->notificationFactory = $notificationFactory;
    }

    #[Route('/api/url', name: 'post_url', methods: ['POST'])]
    public function createUrl(Request $request): JsonResponse
    {
        $request = json_decode($request->getContent(), true);

        $tags = isset($request['tags']) ? $request['tags'] : [];

        if (!$tags) {
            return new JsonResponse(['error' => 'tags are required'], 400);
        }

        foreach ($tags as $tag) {
            if (!is_numeric($tag) || $tag < 1) {
                return new JsonResponse(['error' => 'tags must be numeric'], 400);
            }
        }

        $response = $this->urlService->createUrl($this->user, $tags);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        $followers = $this->user->getFollowers();

        if ($followers instanceof Collection) {
            $followersArray = $followers->toArray();

            foreach ($followersArray as $follower) {
                try {
                    $notification = $this->notificationFactory->createNotification('email');
                    $notification->sendNotification($follower, 'new_url', $response->getUrl());
                } catch (\InvalidArgumentException $e) {
                    // Log the exception
                }
            }
        }


        return new JsonResponse([], 201);
    }
}
