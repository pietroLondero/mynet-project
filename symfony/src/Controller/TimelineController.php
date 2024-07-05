<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TimelineService;

class TimelineController extends AbstractController
{

    public function __construct(
        private TimelineService $timelineService
    ) {
        $this->timelineService = $timelineService;
    }

    #[Route('/api/timeline', name: 'timeline', methods: ['GET'])]
    public function search(Request $request,): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        if ($page < 1 || !is_numeric($page)) {
            return new JsonResponse(['error' => 'Page parameter must be a positive integer'], 400);
        }

        if ($limit < 1 || !is_numeric($limit) || $limit > 100) {
            return new JsonResponse(['error' => 'Limit parameter must be a positive integer less than or equal to 100'], 400);
        }

        $results = $this->timelineService->timeline($page, $limit);


        return new JsonResponse($results);
    }
}
