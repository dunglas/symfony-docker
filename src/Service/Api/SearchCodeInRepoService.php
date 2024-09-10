<?php

namespace App\Service\Api;

use App\SearchInRepo\SearchCodeInRepoStrategyInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchCodeInRepoService
{
const DEFAULT_PAGE = '1';
const DEFAULT_PER_PAGE = '25';

private SearchCodeInRepoStrategyInterface $searchCodeInRepoStrategy;

public function __construct(SearchCodeInRepoStrategyInterface $searchCodeInRepoStrategy)
{
$this->searchCodeInRepoStrategy = $searchCodeInRepoStrategy;
}

public function searchCodeInRepo(
string $code,
string $page = self::DEFAULT_PAGE,
string $perPage = self::DEFAULT_PER_PAGE
): JsonResponse
{
return $this->searchCodeInRepoStrategy->searchCodeInRepo($code, $page, $perPage);
}
}
