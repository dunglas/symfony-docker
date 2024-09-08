<?php

namespace App\Tests\UnitTests;

use PHPUnit\Framework\TestCase;
use App\DTO\CodeSearchResultDTO;

/** @covers \App\DTO\CodeSearchResultDTO */
class CodeSearchResultDTOTest extends TestCase
{
    public function testConstructorAndGetters()
    {
        $ownerName = 'owner';
        $repoName = 'repo';
        $fileName = 'file.php';

        $dto = new CodeSearchResultDTO($ownerName, $repoName, $fileName);

        $this->assertEquals($ownerName, $dto->getOwnerName());
        $this->assertEquals($repoName, $dto->getRepoName());
        $this->assertEquals($fileName, $dto->getFileName());
    }
}
