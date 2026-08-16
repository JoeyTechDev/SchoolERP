<?php

declare(strict_types=1);

namespace SchoolERP\Query\Pagination;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Paginator
 * --------------------------------------------------------------------------
 *
 * Represents a paginated query result.
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
        $this->perPage = max(1, $this->perPage);
        $this->currentPage = max(1, $this->currentPage);
    }

    /**
     * Get items on the current page.
     *
     * @return array<int,mixed>
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * Get total number of records.
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Get records per page.
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get current page number.
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get total number of pages.
     */
    public function lastPage(): int
    {
        if ($this->total === 0) {
            return 1;
        }

        return (int) ceil(
            $this->total / $this->perPage
        );
    }

    /**
     * Determine whether a previous page exists.
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Determine whether a next page exists.
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage();
    }

    /**
     * Get previous page number.
     */
    public function previousPage(): int
    {
        return max(
            1,
            $this->currentPage - 1
        );
    }

    /**
     * Get next page number.
     */
    public function nextPage(): int
    {
        return min(
            $this->lastPage(),
            $this->currentPage + 1
        );
    }

    /**
     * Get first item number displayed on this page.
     */
    public function firstItem(): int
    {
        if ($this->total === 0) {
            return 0;
        }

        return (
            ($this->currentPage - 1)
            * $this->perPage
        ) + 1;
    }

    /**
     * Get last item number displayed on this page.
     */
    public function lastItem(): int
    {
        if ($this->total === 0) {
            return 0;
        }

        return min(
            $this->currentPage * $this->perPage,
            $this->total
        );
    }
}