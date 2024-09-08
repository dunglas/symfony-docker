<?php

namespace App\Service\Api;

use App\SearchInRepo\SearchCodeInRepoStrategyInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchCodeInRepoService
{
    private $searchCodeInRepoStrategy;

    public function __construct(SearchCodeInRepoStrategyInterface $searchCodeInRepoStrategy)
    {
        $this->searchCodeInRepoStrategy = $searchCodeInRepoStrategy;
    }

    public function searchCodeInRepo(string $code): JsonResponse
    {
        return $this->searchCodeInRepoStrategy->searchCodeInRepo($code);
    }
}
