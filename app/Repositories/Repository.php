<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Contracts\RepositoryInterface;
use SchoolERP\ORM\Model;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Base Repository
 * --------------------------------------------------------------------------
 *
 * Provides common CRUD operations for repositories.
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * Model instance.
     */
    protected Model $model;

    /**
     * Constructor.
     */
    public function __construct(
        Model $model
    ) {
        $this->model = $model;
    }

    /**
     * Get all records.
     *
     * @return array<int,mixed>
     */
    public function all(): array
    {
        return $this->model
            ->query()
            ->get();
    }

    /**
     * Find a record by ID.
     */
    public function find(
        int $id
    ): mixed {
        return $this->model->find($id);
    }

    /**
     * Create a new record.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create($data);
    }

    /**
     * Update a record.
     *
     * Returns true when the update operation succeeds.
     */
    public function update(
        int $id,
        array $data
    ): bool {
        $model = $this->find($id);

        if ($model === null) {
            return false;
        }

        $result = $model->update($data);

        return (bool) $result;
    }

    /**
     * Delete a record.
     */
    public function delete(
        int $id
    ): bool {
        $model = $this->find($id);

        if ($model === null) {
            return false;
        }

        $result = $model->delete();

        return (bool) $result;
    }
}