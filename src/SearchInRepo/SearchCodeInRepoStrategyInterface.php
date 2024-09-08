<?php

namespace App\SearchInRepo;

use Symfony\Component\HttpFoundation\JsonResponse;

interface SearchCodeInRepoStrategyInterface
{
 public function searchCodeInRepo(string $code): JsonResponse;
}
