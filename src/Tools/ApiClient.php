<?php

namespace App\Tools;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private string $zohoUrl;
    private string $optipackUrl;
    private string $optipackToken;
    private string $lastResponse;

    function __construct(ParameterBagInterface       $params,
                         private HttpClientInterface $client,
                         private LoggerInterface     $logger)
    {
        $this->zohoUrl = $params->get("app.zoho_url");

        $optipackApi = $params->get("app.optipack_api");
        $this->optipackUrl = $optipackApi["url"];
        $this->optipackToken = $optipackApi["token"];
    }

    public function getLastResponse(): string
    {
        return $this->lastResponse;
    }

    public function getPackageNumber(string $igetPo): ?string
    {
        $zohoId = $this->performGet($this->zohoUrl . $igetPo);
        $packageNumber = null;

        if ($zohoId) {
            $optipackJson = $this->performGet($this->optipackUrl . $zohoId,
                ["headers" => ["X-API-TOKEN" => $this->optipackToken]]);

            if ($optipackJson)
                try { // Response is loaded lazily
                    $packageNumber = json_decode($optipackJson)
                        ->results[0]
                        ->excerpt
                        ->structured
                        ->packingUnits[0]
                        ->loadingDevice;
                } catch (\Exception $e) {
                    $this->lastResponse = $e->getMessage();
                }
        }

        return $packageNumber;
    }

    private function performGet(string $url, array $options = []): ?string
    {
        $response = $this->client->request("GET", $url, $options);
        $statusCode = $response->getStatusCode();
        $this->lastResponse = $content = $response->getContent(false);

        $this->logger->info(__METHOD__ . "($url): $statusCode - '$content'");

        return $statusCode == Response::HTTP_OK ? $content : null;
    }
}
