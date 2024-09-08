<?php

namespace App\DTO;

use App\DTO\CodeSearchResultDTO;
use Countable;
use IteratorAggregate;
use ArrayIterator;

class CodeSearchResultDTOCollection implements Countable, IteratorAggregate
{
    private array $items = [];

    public function add(CodeSearchResultDTO $dto): void
    {
        $this->items[] = $dto;
    }

    public function remove(CodeSearchResultDTO $dto): void
    {
        $this->items = array_filter($this->items, fn($item) => $item !== $dto);
    }

    public function get(int $index): ?CodeSearchResultDTO
    {
        return $this->items[$index] ?? null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function toArray(): array
    {
        return array_map(fn($item) => [
            'ownerName' => $item->getOwnerName(),
            'repoName' => $item->getRepoName(),
            'fileName' => $item->getFileName(),
            'score' => $item->getScore()
        ], $this->items);
    }

    public function sortBy(string $parameter): void
    {
        usort($this->items, fn($a, $b) => $b->{'get' . ucfirst($parameter)}() <=> $a->{'get' . ucfirst($parameter)}());
    }
}
