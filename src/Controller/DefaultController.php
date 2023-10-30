<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/')]
    public function welcome(): Response
    {
        $this->logger->log('error','hello there');
        return new Response(
            '<html><body>I\'ve logged a message for you</body></html>'
        );
    }
}
