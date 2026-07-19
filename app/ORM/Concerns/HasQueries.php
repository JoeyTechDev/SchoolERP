<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

use SchoolERP\Query\QueryBuilder;

    /**
     * Query methods for ORM models.
     */
    trait HasQueries
    {

    /**
     * Ensure the Query Builder is initialized.
     */
    private function initializeQuery(): void
    {
        $this->query->table($this->table);
    }

    /**
     * Get all records.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->query
            ->table($this->table)
            ->select(['*'])
            ->get();
    }

    /**
     * Find a record by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?static
{
    $record = $this->query
        ->table($this->table)
        ->where('id', '=', $id)
        ->first();

    if ($record === null) {
        return null;
    }

    return (new static())->fill($record);
}

/**
 * Create a new record.
 *
 * @param array<string,mixed> $attributes
 */
public function create(array $attributes): int
{
    $attributes = $this->fillable($attributes);

    $attributes = $this->addCreationTimestamps(
        $attributes
    );

    return $this->query
        ->table($this->table)
        ->insert($attributes);
}

/**
 * Update records.
 *
 * @param array<string,mixed> $attributes
 */
public function update(array $attributes): int
{
    $attributes = $this->filterFillable($attributes);

    return $this->query->update($attributes);
}
 
/**
 * Save the current model.
 */
public function save(): bool
{
    if (!$this->isDirty()) {
        return true;
    }
    
    $this->touch();
    $dirty = $this->getDirty();
    $dirty = $this->filterFillable($dirty);
    
    $id = $this->attributes['id'];

    $this->query
        ->table($this->table)
        ->where('id', '=', $id)
        ->update($dirty);

    $this->fill($this->attributes);

    return true;
}

/**
 * Delete records.
 */
public function delete(): bool
{
    if (!isset($this->attributes['id'])) {
        throw new \RuntimeException(
            'Cannot delete a model without an ID.'
        );
    }

    $affected = $this->query
        ->table($this->table)
        ->where('id', '=', $this->attributes['id'])
        ->delete();

    return $affected > 0;
}

/**
 * Determine whether records exist.
 */
public function exists(): bool
{
    return $this->first() !== null;
}

/**
 * Get the first record or throw.
 *
 * @return array<string,mixed>
 */
public function firstOrFail(): array
{
    $record = $this->first();

    if ($record === null) {
        throw new \RuntimeException(
            'Record not found.'
        );
    }

    return $record;
}

public function count(): int
{
    return $this->query->count();
}

/**
 * Begin a new model query.
 */
public function query(): static
{
    $this->initializeQuery();

    return $this;
}

    /**
     * Add a WHERE clause.
     */
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): static {

        $this->query->where(
            $column,
            $operator,
            $value
        );

        return $this;
    }

    /**
     * Execute the query.
     *
     * @return array<int,array<string,mixed>>
     */
    public function get(): array
    {
        return $this->query->get();
    }

/**
 * Get the first matching record.
 */
public function first(): ?static
{
    $record = $this->query->first();

    if ($record === null) {
        return null;
    }

    $model = (new static())->fill($record);

    foreach ($this->query->getEagerLoads() as $relation) {

        if (!method_exists($model, $relation)) {
            continue;
        }

        $model->setRelation(
            $relation,
            $model->{$relation}()->get()
        );
    }

    return $model;
}

/**
 * Forward unknown methods to the Query Builder.
 */
public function __call(
    string $method,
    array $arguments
): mixed {

    $this->initializeQuery();

    /*
     * Local Scope
     */
    $scope = 'scope' . ucfirst($method);

    if (method_exists($this, $scope)) {
        return $this->$scope(...$arguments);
    }

    /*
     * Forward to Query Builder
     */
    if (!method_exists($this->query, $method)) {
        throw new \BadMethodCallException(
            sprintf(
                'Method %s::%s does not exist.',
                static::class,
                $method
            )
        );
    }

    $result = $this->query->$method(...$arguments);

    if ($result instanceof \SchoolERP\Query\QueryBuilder) {
        return $this;
    }

    return $result;
}

}