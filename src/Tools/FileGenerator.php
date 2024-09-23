<?php

namespace App\Tools;

use Knp\Snappy\Image;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class FileGenerator
{
    /** Length/width/height by package number */
    const PKG_DIMENSIONS = [
        "K01" => [350, 335, 145],
        "K03" => [360, 355, 216],
        "K04" => [515, 330, 216],
        "K07" => [580, 385, 216],
        "K08" => [315, 225, 145],
        "FlapIt" => [325, 148, 80]
    ];
    /** Logo file by client */
    const CLIENT_FILES = [
        110 => "tq logo_claim.svg"
    ];

    private string $baseDir;

    function __construct(KernelInterface     $appKernel,
                         private Environment $twig,
                         private Image       $snappyImg)
    {
        $this->baseDir = $appKernel->getProjectDir() . "/public";
    }

    public function create(int $clientId, string $packageNumber, int $line): string
    {
        if (!isset(self::PKG_DIMENSIONS[$packageNumber]))
            throw new \InvalidArgumentException("Invalid package number: $packageNumber");
        $pkgDimensions = self::PKG_DIMENSIONS[$packageNumber];
        $width = $pkgDimensions[0];

        if (!in_array($line, [1, 2]))
            throw new \InvalidArgumentException("Invalid line: $line");
        $height = $pkgDimensions[$line == 1 ? 2 : 1];

        $pathPng = sys_get_temp_dir() . '/' . uniqid() . ".png";
        if (file_exists($pathPng))
            unlink($pathPng);

        $this->snappyImg->generateFromHtml($this->twig->render("pdf.twig", [
            "logoSrc" => "$this->baseDir/img/" . self::CLIENT_FILES[$clientId]
        ]), $pathPng, [
            "enable-local-file-access" => true,
            "width" => $width,
            "height" => $height
        ]);

        return $pathPng;
    }
}
