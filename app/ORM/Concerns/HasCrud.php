<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * CRUD operations for ORM models.
 */
trait HasCrud
{
    /**
     * Create a new record.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): int
    {
        return $this->query
            ->table($this->table)
            ->insert($data);
    }

    /**
     * Update matching records.
     *
     * @param array<string,mixed> $data
     */
    public function update(array $data): int
    {
        return $this->query->update($data);
    }

    /**
     * Delete matching records.
     */
    public function delete(): int
    {
        return $this->query->delete();
    }
}