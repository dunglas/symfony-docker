<?php

namespace App\Controller;

use App\Tools\ApiClient;
use App\Tools\FileGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    function __construct(private ApiClient       $apiClient,
                         private FileGenerator   $fileGenerator,
                         private LoggerInterface $logger)
    {
    }

    /** Action for demo layout - https://localhost/demo */
    #[Route('/demo', name: 'demo')]
    public function demo(): Response
    {
        return $this->fileResponse(110, "K07", 1);
    }

    /** Default file creation action - z.B. https://localhost/110/1128944659/1 */
    #[Route('/{clientId}/{igetPo}/{line}', name: 'default')]
    public function get(int $clientId, string $igetPo, int $line): Response
    {
        $this->logger->info(__METHOD__ . "($clientId, $igetPo, $line)");

        if (!isset(FileGenerator::CLIENT_FILES[$clientId]))
            $response = new Response("Unsupported client id - $clientId", Response::HTTP_NOT_IMPLEMENTED);
        elseif ($packageNumber = $this->apiClient->getPackageNumber($igetPo)) {
            $response = $this->fileResponse($clientId, $packageNumber, $line);
        } else {
            $response = new Response($this->apiClient->getLastResponse(), Response::HTTP_BAD_GATEWAY);
        }

        return $response;
    }

    private function fileResponse(int $clientId, string $packageNumber, int $line): Response
    {
        try {
            return new BinaryFileResponse($this->fileGenerator->create($clientId, $packageNumber, $line));
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
