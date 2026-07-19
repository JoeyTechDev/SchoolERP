<?php

declare(strict_types=1);

namespace SchoolERP\Query\Pagination;

/**
 * Pagination result object.
 */
final class Paginator
{
    /**
     * @param array<int,mixed> $items
     */
    public function __construct(
        private array $items,
        private int $total,
        private int $perPage,
        private int $currentPage
    ) {
    }

    /**
     * Items on the current page.
     *
     * @return array<int,mixed>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Total records.
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Records per page.
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Current page.
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Total number of pages.
     */
    public function lastPage(): int
    {
        return (int) ceil(
            $this->total / $this->perPage
        );
    }

    /**
     * Is there another page?
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage();
    }
}