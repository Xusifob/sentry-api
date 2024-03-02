<?php

namespace App\ApiPlatform;

use ApiPlatform\State\Pagination\PaginatorInterface;

class Paginator implements \IteratorAggregate, \Countable, PaginatorInterface
{
    public function __construct(private readonly array $items, private readonly int $totalItems, private readonly int $itemsPerPage, private readonly int $currentPage)
    {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function count(): int
    {
        return $this->totalItems;
    }

    public function getTotalItems(): float
    {
        return $this->totalItems;
    }

    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getLastPage(): float
    {
        return $this->totalItems / $this->itemsPerPage;
    }
}
