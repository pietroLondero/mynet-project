<?php

namespace App\Services;

use App\Repository\TagRepository;
use App\Repository\UrlRepository;

class TimelineService
{
    public function __construct(
        private UrlRepository $urlRepository,
        private TagRepository $tagRepository
    ) {
        $this->urlRepository = $urlRepository;
        $this->tagRepository = $tagRepository;
    }

    public function timeline(int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

        $urls = $this->urlRepository->getByTimeInsert($offset, $limit);

        foreach ($urls as &$url) {
            $url['tags'] = $this->tagRepository->getTagsForUrl($url['id']);
        }

        $total = $this->urlRepository->countAll();

        return [
            "data" => $urls,
            "total" => $total,
            "page" => $page,
            "limit" => $limit
        ];
    }
}
