<?php

namespace App\Tests;

use App\Tools\ApiClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClientTest extends KerneltestCase
{
    private string $mockUrl = "https://example.com/";
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = static::getContainer();
    }

    private function apiClient(): ApiClient
    {
        return $this->container->get(ApiClient::class);
    }

    function testExample()
    {
        $this->assertSame("K07", $this->apiClient()->getPackageNumber(1128944659));
    }

    private function mockParameters(string $zohoUrl, array $optipackApi): void
    {
        $this->container->set(ParameterBagInterface::class,
            $params = $this->createMock(ParameterBagInterface::class));
        $params->expects($this->exactly(2))->method("get")
            ->with($this->callback(function ($key) {
                return in_array($key, ["app.zoho_url", "app.optipack_api"]);
            }))
            ->willReturnOnConsecutiveCalls($zohoUrl, $optipackApi);
    }

    function testErrorApi1()
    {
        $this->mockParameters($this->mockUrl . uniqid(), ["url" => '', "token" => '']);

        $igetPo = uniqid();
        $zohoResponse = uniqid();

        $this->container->set(HttpClientInterface::class,
            new MockHttpClient(new MockResponse($zohoResponse, ["http_code" => Response::HTTP_NOT_FOUND])));

        $apiClient = $this->apiClient();
        $this->assertNull($apiClient->getPackageNumber($igetPo));
        $this->assertSame($zohoResponse, $apiClient->getLastResponse());
    }

    private function mockApiResponses(string $igetPo, $optipackResponse, $optipackCode): void
    {
        $this->mockParameters(
            $zohoUrl = $this->mockUrl . uniqid(),
            $optipackApi = [
                "url" => $this->mockUrl . uniqid(),
                "token" => uniqid()
            ]);

        $zohoId = uniqid();
        $this->container->set(HttpClientInterface::class, new MockHttpClient([
            function ($method, $url) use ($zohoUrl, $igetPo, $zohoId): MockResponse {
                $this->assertSame('GET', $method);
                $this->assertSame($url, $zohoUrl . $igetPo);
                return new MockResponse($zohoId, ["http_code" => Response::HTTP_OK]);
            },
            function ($method, $url, $options) use ($optipackApi, $zohoId, $optipackResponse, $optipackCode): MockResponse {
                $this->assertSame('GET', $method);
                $this->assertSame($url, $optipackApi["url"] . $zohoId);
                $this->assertSame($options["headers"][0], "X-API-TOKEN: " . $optipackApi["token"]);
                return new MockResponse($optipackResponse, ["http_code" => $optipackCode]);
            }
        ]));
    }

    function testErrorApi2()
    {
        $this->mockApiResponses(
            $igetPo = uniqid(),
            $optipackResponse = uniqid(), Response::HTTP_INTERNAL_SERVER_ERROR);
        $apiClient = $this->apiClient();
        $this->assertNull($apiClient->getPackageNumber($igetPo));
        $this->assertSame($optipackResponse, $apiClient->getLastResponse());
    }

    function testInvalidOptipackData()
    {
        $this->mockApiResponses(
            $igetPo = uniqid(), uniqid(), Response::HTTP_OK);

        $apiClient = $this->apiClient();
        $this->assertNull($apiClient->getPackageNumber($igetPo));
        $this->assertSame('Attempt to read property "results" on null', $apiClient->getLastResponse());
    }

    function testSuccess()
    {
        $this->mockApiResponses(
            $igetPo = uniqid(),
            $optipackResponse = json_encode([
                "results" => [[
                    "excerpt" => [
                        "structured" => [
                            "packingUnits" => [[
                                "loadingDevice" => $packageNumber = uniqid()
                            ]]]]]]]), Response::HTTP_OK);

        $apiClient = $this->apiClient();
        $this->assertSame($packageNumber, $apiClient->getPackageNumber($igetPo));
        $this->assertSame($optipackResponse, $apiClient->getLastResponse());
    }
}
