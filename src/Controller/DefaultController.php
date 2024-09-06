<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    const string ENVKEY_DELIVERY_URL = "APP_GET_DELIVERY_URL";

    function __construct(
        private KernelInterface     $appKernel,
        private HttpClientInterface $client,
        private Pdf                 $snappyPdf)
    {
    }

    /** Action for demo layout - https://localhost/demo */
    #[Route('/demo', name: 'demo')]
    public function demo(): Response
    {
        return $this->createPdfResponse("demoFile", ["title" => "Demo File"]);
    }

    #[Route('/{deliveryId}', name: 'default')]
    public function get(string $deliveryId): Response
    {
        $rGetDelivery = $this->client->request("GET", $_ENV[self::ENVKEY_DELIVERY_URL] . "/$deliveryId");
        try { // Response is loaded lazily
            $content = $rGetDelivery->getContent();
        } catch (ClientException $e) {
            $content = $e->getMessage();
        }

        $response = new Response($content,
            $code = $rGetDelivery->getStatusCode());

        if ($code == Response::HTTP_OK) {
            try {
                $response = $this->createPdfResponse($deliveryId, json_decode($content, true));
            } catch (\Exception $e) {
                $response = new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $response;
    }

    private function createPdfResponse(string $fileName, array $content): BinaryFileResponse
    {
        $outFile = sys_get_temp_dir() . "/$fileName.pdf";

        if (file_exists($outFile))
            unlink($outFile);

        $baseDir = $this->appKernel->getProjectDir() . "/public";

        $this->snappyPdf->generateFromHtml($this->renderView("pdf.twig",
            $content + [
                "cssHref" => "$baseDir/css/pdf.css",
                "logoSrc" => "$baseDir/img/logo.svg"
            ]),
            $outFile,
            ["enable-local-file-access" => true]);

        return new BinaryFileResponse($outFile);
    }
}
