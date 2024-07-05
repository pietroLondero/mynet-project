<?php

namespace App\Controller;

use App\Services\LikeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\SearchService;

class SearchController extends AbstractController
{

    public function __construct(
        private SearchService $searchService,
        private LikeService $likeService
    ) {
        $this->searchService = $searchService;
        $this->likeService = $likeService;
    }

    #[Route('/api/search', name: 'search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        if (!$query) {
            return new JsonResponse(['error' => 'Query parameter is required'], 400);
        }
        if ($page < 1 || !is_numeric($page)) {
            return new JsonResponse(['error' => 'Page parameter must be a positive integer'], 400);
        }

        if ($limit < 1 || !is_numeric($limit) || $limit > 100) {
            return new JsonResponse(['error' => 'Limit parameter must be a positive integer less than or equal to 100'], 400);
        }


        $results = $this->searchService->searchByTag($query, $page, $limit);

        return new JsonResponse($results);
    }
}
