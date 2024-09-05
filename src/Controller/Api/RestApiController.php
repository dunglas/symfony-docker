<?php

namespace App\Controller\Api;
use App\SearchInRepo\SearchCodeInRepoStrategyInterface;
use Symfony\Component\HttpClient\HttpClient;
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
    public function searchCodeInRepo(SearchCodeInRepoStrategyInterface $repoStrategy, string $code): jsonResponse
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.github.com/search/code?q=' . $code );
        return new JsonResponse($response->toArray());
    }
}
