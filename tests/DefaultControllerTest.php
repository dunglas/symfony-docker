<?php

namespace App\Tests;

use App\Controller\DefaultController;
use App\Tools\ApiClient;
use App\Tools\FileGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

/** @see DefaultController */
class DefaultControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    function testDemo()
    {
        $this->client->request('GET', "/demo");
        $this->assertInstanceOf(BinaryFileResponse::class,
            $response = $this->client->getResponse());
        $this->assertFileExists($response->getFile()->getPathname());
    }

    private function request(string $clientId, string $igetPo, string $line): Response
    {
        $this->client->request('GET', "/$clientId/$igetPo/$line");
        return $this->client->getResponse();
    }

    function testInvalidClientId()
    {
        $response = $this->request(
            $clientId = 1, 0, 0);
        $this->assertSame(Response::HTTP_NOT_IMPLEMENTED, $response->getStatusCode());
        $this->assertSame("Unsupported client id - $clientId", $response->getContent());
    }

    function testApiError()
    {
        $this->client->getContainer()->set(ApiClient::class,
            $apiClient = $this->createMock(ApiClient::class));
        $apiClient->expects($this->once())->method("getPackageNumber")
            ->with(
                $igetPo = uniqid())
            ->willReturn(null);
        $apiClient->expects($this->once())->method("getLastResponse")
            ->willReturn(
                $apiResponse = uniqid());

        $response = $this->request(110, $igetPo, 0);
        $this->assertSame(Response::HTTP_BAD_GATEWAY, $response->getStatusCode());
        $this->assertSame($apiResponse, $response->getContent());
    }

    private function requestFileCreation(string $filePath): Response
    {
        $this->client->getContainer()->set(ApiClient::class,
            $apiClient = $this->createMock(ApiClient::class));
        $apiClient->expects($this->once())->method("getPackageNumber")
            ->with(
                $igetPo = uniqid())
            ->willReturn(
                $packageNumber = uniqid());

        $this->client->getContainer()->set(FileGenerator::class,
            $fileGenerator = $this->createMock(FileGenerator::class));
        $fileGenerator->expects($this->once())->method("create")
            ->with(
                $clientId = 110, $packageNumber,
                $line = 0)
            ->willReturn($filePath);

        return $this->request($clientId, $igetPo, $line);
    }

    function testFileNotFound()
    {
        $response = $this->requestFileCreation(
            $notExistingFilePath = uniqid());
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame("The file \"$notExistingFilePath\" does not exist", $response->getContent());
    }

    function testFileCreation()
    {
        $response = $this->requestFileCreation(
            $filePath = __FILE__);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertEquals(new File($filePath), $response->getFile());
    }
}
