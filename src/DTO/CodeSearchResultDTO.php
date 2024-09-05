<?php

namespace App\DTO;

class CodeSearchResultDTO
{
    private string $ownerName;
    private string $repoName;
    private string $fileName;

    public function __construct($ownerName, $repoName, $fileName)
    {
        $this->ownerName = $ownerName;
        $this->repoName = $repoName;
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    /**
     * @return string
     */
    public function getRepoName(): string
    {
        return $this->repoName;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
