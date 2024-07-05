<?php

namespace App\Services;

use App\Repository\TagRepository;
use App\Repository\UrlRepository;

class SearchService
{

    public function __construct(
        private TagRepository $tagRepository,
        private UrlRepository $urlRepository
    ) {
        $this->tagRepository = $tagRepository;
        $this->urlRepository = $urlRepository;
    }

    public function searchByTag(string $query, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $urls = $this->urlRepository->findByTag($query, $offset, $limit);
        foreach ($urls as &$url) {
            $url['tags'] = $this->tagRepository->getTagsForUrl($url['id']);
        }

        $total = $this->urlRepository->countByTag($query);

        return [
            "data" => $urls,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        ];
    }
}
