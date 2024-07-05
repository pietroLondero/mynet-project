<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\UserService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private User $user;
    public function __construct(
        private UserService $userService,
        private TokenStorageInterface $tokenStorage
    ) {
        $this->userService = $userService;
        $this->user = $this->tokenStorage->getToken()->getUser();
    }

    #[Route('/api/users_urls', name: 'users_urls', methods: ['GET'])]
    public function getUserFollowedUrls(Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        if ($page < 1 || !is_numeric($page)) {
            return new JsonResponse(['error' => 'Page parameter must be a positive integer'], 400);
        }

        if ($limit < 1 || !is_numeric($limit) || $limit > 100) {
            return new JsonResponse(['error' => 'Limit parameter must be a positive integer less than or equal to 100'], 400);
        }

        $response = $this->userService->getUserFollowedUrls($this->user, $page, $limit);

        return new JsonResponse($response, 200);
    }
}
