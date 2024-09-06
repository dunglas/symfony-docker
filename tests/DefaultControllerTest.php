<?php

namespace App\Tests;

use App\Controller\DefaultController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/** @see DefaultController */
class DefaultControllerTest extends WebTestCase
{
    private function performRequest(string $deliveryId, int $code, string $content): Response
    {
        $client = static::createClient();
        $client->getContainer()->set(HttpClientInterface::class,
            new MockHttpClient(new MockResponse($content, ["http_code" => $code])));

        $client->request('GET', "/$deliveryId");
        return $client->getResponse();
    }

    private function getContentFromAssertedApiCode(string $deliveryId, int $code, string $content): string
    {
        $response = $this->performRequest($deliveryId, $code, $content);
        $this->assertEquals($code, $response->getStatusCode());

        return $response->getContent();
    }

    function testApiError404()
    {
        $content = $this->getContentFromAssertedApiCode(
            $deliveryId = uniqid(),
            $code = Response::HTTP_NOT_FOUND, '');
        $this->assertEquals("HTTP $code returned for \"" . $_ENV[DefaultController::ENVKEY_DELIVERY_URL] . "/$deliveryId\".", $content);
    }

    function testApiError500()
    {
        $content = $this->getContentFromAssertedApiCode(
            $deliveryId = uniqid(),
            $code = Response::HTTP_INTERNAL_SERVER_ERROR,
            $apiResponse = __METHOD__);
        $this->assertStringContainsString($apiResponse, $content);
        $this->assertStringContainsString("HTTP $code returned for &quot;https://torquato.de/$deliveryId&quot;.", $content);
    }

    function testCreateError()
    {
        $response = $this->performRequest(uniqid(), Response::HTTP_OK, "[]");
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals('Variable "title" does not exist.', $response->getContent());
    }

    function testFileCreation()
    {
        $this->getContentFromAssertedApiCode(
            $deliveryId = uniqid(), Response::HTTP_OK,
            json_encode(["title" => __METHOD__]));
        $this->assertFileExists(sys_get_temp_dir() . "/$deliveryId.pdf");
    }

    function testDemo()
    {
        static::createClient()->request('GET', "/demo");
        $this->assertFileExists(sys_get_temp_dir() . "/demoFile.pdf");
    }
}
