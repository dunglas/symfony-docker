<?php

namespace App\Controller\Api;
use App\SearchInRepo\GithubSearchCodeInRepoStrategy;
use App\Service\Api\SearchCodeInRepoService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RestApiController
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function searchCodeInRepo(string $code): JsonResponse
    {
        $searchInCodeReposeService = new SearchCodeInRepoService(new GithubSearchCodeInRepoStrategy());
        return $searchInCodeReposeService->searchCodeInRepo($code);
    }
}
