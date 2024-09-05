<?php

namespace App\SearchInRepo;
use Symfony\Component\HttpFoundation\JsonResponse;

class GithubSearchCodeInRepoStrategy implements SearchCodeInRepoStrategyInterface
{
    public function searchCodeInRepo(string $repo, string $code): JsonResponse
    {
        return JsonResponse::create(['repo' => $repo, 'code' => $code]);
    }
}
