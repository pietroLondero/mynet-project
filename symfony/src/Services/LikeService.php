<?php

namespace App\Services;

use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class LikeService
{
    public function __construct(
        private UrlRepository $urlRepository,
        private userRepository $userRepository
    ) {
        $this->urlRepository = $urlRepository;
        $this->userRepository = $userRepository;
    }

    public function likeUrl(int $userId, int $postId): JsonResponse|Url
    {
        $user = $this->userRepository->getUserById($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (!$this->urlRepository->getUrlById($postId)) {
            return new JsonResponse(['error' => 'Post not found'], 404);
        }

        if ($this->urlRepository->userLikesUrl($user, $postId)) {
            return new JsonResponse(['error' => 'User already liked this post'], 400);
        }

        $this->urlRepository->likeUrl($user, $postId);

        return $this->urlRepository->getUrlById($postId);
    }
}
