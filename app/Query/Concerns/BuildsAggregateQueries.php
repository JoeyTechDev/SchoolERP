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
        $sql = sprintf(
            'SELECT COUNT(*) AS aggregate FROM %s',
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

        return (int) $result[0]['aggregate'];
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

}