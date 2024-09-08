<?php

namespace App\SearchInRepo;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GithubSearchCodeInRepoStrategy implements SearchCodeInRepoStrategyInterface
{
    const GITHUB_API_URL = 'https://api.github.com/search/code?q=';

    public function __construct()
    {
    }

    /**
     * @param string $code
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function searchCodeInRepo(string $code): JsonResponse
    {
        $client = HttpClient::create();
        $response = $client->request('GET', self::GITHUB_API_URL . $code );
        return new JsonResponse($response->toArray());
    }
}
