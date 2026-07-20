<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Contracts\RepositoryInterface;
use SchoolERP\ORM\Model;

abstract class Repository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): array
    {
        return $this->model
            ->query()
            ->get();
    }

    public function find(int $id): mixed
    {
        return $this->model->find($id);
    }

    public function create(array $data): mixed
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->find($id);

        if ($model === null) {
            return false;
        }

        return $model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->find($id);

        if ($model === null) {
            return false;
        }

        return $model->delete();
    }
}