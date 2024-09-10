<?php
namespace App\DTO;

class CodeSearchResultDTO
{
public function __construct(
public readonly string $ownerName,
public readonly string $repoName,
public readonly string $fileName,
public readonly float $score
) {}

public function getOwnerName(): string
{
return $this->ownerName;
}

public function getRepoName(): string
{
return $this->repoName;
}

public function getFileName(): string
{
return $this->fileName;
}

public function getScore(): float
{
return $this->score;
}
}
