<?php

namespace App\Services;

use App\Entity\Url;
use App\Entity\User;
use App\Repository\TagRepository;
use App\Repository\UrlRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class UrlService
{
    public function __construct(
        private UrlRepository $urlRepository,
        private TagRepository $tagRepository
    ) {
        $this->urlRepository = $urlRepository;
        $this->tagRepository = $tagRepository;
    }

    public function getUrlById(int $id)
    {
        return $this->urlRepository->getUrlById($id);
    }

    public function createUrl(User $user, array $tags): JsonResponse|Url
    {

        $tags = $this->tagRepository->getTags($tags);

        $url = $this->urlRepository->addUrl($user, $tags);

        if ($url) {
            return $url;
        }

        return new JsonResponse(['error' => 'Error creating url'], 500);
    }

    public function getUserUrl(int $urlId)
    {
        return $this->urlRepository->getUserUrl($urlId);
    }
}
