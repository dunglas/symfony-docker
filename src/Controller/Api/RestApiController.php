<?php

namespace App\Controller\Api;
use App\SearchInRepo\GithubSearchCodeInRepoStrategy;
use App\Service\Api\SearchCodeInRepoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestApiController
{

    public function searchCodeInRepo(Request $request): JsonResponse
    {
        $searchInCodeReposeService = new SearchCodeInRepoService(new GithubSearchCodeInRepoStrategy());
        return $searchInCodeReposeService->searchCodeInRepo($request->get('code'));
    }
}
