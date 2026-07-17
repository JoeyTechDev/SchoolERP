<?php

declare(strict_types=1);

namespace SchoolERP\Query\Concerns;

/**
 * Aggregate query methods.
 */
trait BuildsAggregateQueries
{
    /**
     * Count records.
     */
    public function count(): int
{
    return (int) $this->aggregate('COUNT');
}

/**
 * Execute an aggregate function.
 */
private function aggregate(
    string $function,
    string $column = '*'
): mixed {

    $sql = sprintf(
        'SELECT %s(%s) AS aggregate FROM %s',
        strtoupper($function),
        $column,
        $this->state->table
    );

    if (!empty($this->wheres)) {
        $sql .= ' WHERE ' . implode(' ', $this->wheres);
    }

    $result = $this->database->select(
        $sql,
        $this->bindings
    );

    $this->reset();

    return $result[0]['aggregate'];
}

/**
 * Calculate the sum of a column.
 */
public function sum(string $column): float
{
    return (float) $this->aggregate('SUM', $column);
}
  
/**
 * Calculate the average of a column.
 */
public function avg(string $column): float
{
    return (float) $this->aggregate('AVG', $column);
}

/**
 * Get the minimum value of a column.
 */
public function min(string $column): mixed
{
    return $this->aggregate('MIN', $column);
}

/**
 * Get the maximum value of a column.
 */
public function max(string $column): mixed
{
    return $this->aggregate('MAX', $column);
}

/**
 * Determine whether records exist.
 */
public function exists(): bool
{
    return $this->count() > 0;
}


}