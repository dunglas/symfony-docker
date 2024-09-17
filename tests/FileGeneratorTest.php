<?php

namespace App\Tests;

use App\Tools\FileGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileGeneratorTest extends KernelTestCase
{
    private function fileGenerator(): FileGenerator
    {
        return static::getContainer()->get(FileGenerator::class);
    }

    function testInvalidPackageNumber()
    {
        $packageNumber = uniqid();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid package number: $packageNumber");
        $this->fileGenerator()->create(1, $packageNumber, 0);
    }

    function testInvalidLine()
    {
        $line = 0;
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid line: $line");
        $this->fileGenerator()->create(0, array_keys(FileGenerator::PKG_DIMENSIONS)[0], $line);
    }

    function testCreate()
    {
        $this->assertFileExists($this->fileGenerator()->create(
            array_keys(FileGenerator::CLIENT_FILES)[0],
            array_keys(FileGenerator::PKG_DIMENSIONS)[0],
            2));
    }
}
