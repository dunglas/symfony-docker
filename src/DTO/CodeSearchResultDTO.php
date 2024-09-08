<?php

namespace App\DTO;

class CodeSearchResultDTO
{
    private string $ownerName;
    private string $repoName;
    private string $fileName;
    private float $score;

    public function __construct($ownerName, $repoName, $fileName, $score)
    {
        $this->ownerName = $ownerName;
        $this->repoName = $repoName;
        $this->fileName = $fileName;
        $this->score = $score;
    }

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
