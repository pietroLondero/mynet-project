<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\UrlRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private TagRepository $tagRepository
    ) {
        $this->userRepository = $userRepository;
        $this->tagRepository = $tagRepository;
    }

    public function getUserFollowedUrls(User $user, int $page, int $limit)
    {
        $offset = ($page - 1) * $limit;

        $urls = $this->userRepository->getUserFollowedUrls($user, $offset, $limit);

        foreach ($urls as &$url) {
            $url['tags'] = $this->tagRepository->getTagsForUrl($url['id']);
        }

        $total = $this->userRepository->countUserFollowedUrls($user);

        return [
            "data" => $urls,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        ];
    }

    public function getUserFromId(int $id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function userFollowsUser(User $user, User $followedUser): bool
    {
        return $this->userRepository->userFollowsUser($user, $followedUser);
    }

    public function getFollowers(User $user): Collection
    {
        return $this->userRepository->getFollowers($user);
    }
}
