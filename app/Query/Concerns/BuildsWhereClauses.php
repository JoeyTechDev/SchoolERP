<?php

declare(strict_types=1);

namespace SchoolERP\Query\Concerns;

use InvalidArgumentException;

/**
 * Builds all WHERE clauses.
 */
trait BuildsWhereClauses
{
    /**
     * Add a WHERE clause.
     */
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): self {

        if (!empty($this->wheres)) {
            $this->wheres[] = 'AND';
        }

        $this->wheres[] = sprintf(
            '%s %s ?',
            $column,
            $operator
        );

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add an OR WHERE clause.
     */
    public function orWhere(
        string $column,
        string $operator,
        mixed $value
    ): self {

        if (!empty($this->wheres)) {
            $this->wheres[] = 'OR';
        }

        $this->wheres[] = sprintf(
            '%s %s ?',
            $column,
            $operator
        );

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Add a WHERE IN clause.
     *
     * @param array<int,mixed> $values
     */
    public function whereIn(
        string $column,
        array $values
    ): self {

        if ($values === []) {
            throw new InvalidArgumentException(
                'whereIn() requires at least one value.'
            );
        }

        if (!empty($this->wheres)) {
            $this->wheres[] = 'AND';
        }

        $placeholders = implode(
            ', ',
            array_fill(0, count($values), '?')
        );

        $this->wheres[] = sprintf(
            '%s IN (%s)',
            $column,
            $placeholders
        );

        $this->bindings = array_merge(
            $this->bindings,
            $values
        );

        return $this;
    }

    /**
     * Add a WHERE BETWEEN clause.
     *
     * @param array{0:mixed,1:mixed} $values
     */
    public function whereBetween(
        string $column,
        array $values
    ): self {

        if (count($values) !== 2) {
            throw new InvalidArgumentException(
                'whereBetween requires exactly two values.'
            );
        }

        if (!empty($this->wheres)) {
            $this->wheres[] = 'AND';
        }

        $this->wheres[] = sprintf(
            '%s BETWEEN ? AND ?',
            $column
        );

        $this->bindings[] = $values[0];
        $this->bindings[] = $values[1];

        return $this;
    }

    /**
     * Add a WHERE LIKE clause.
     */
    public function whereLike(
        string $column,
        string $pattern
    ): self {

        if (!empty($this->wheres)) {
            $this->wheres[] = 'AND';
        }

        $this->wheres[] = sprintf(
            '%s LIKE ?',
            $column
        );

        $this->bindings[] = $pattern;

        return $this;
    }

    /**
     * Add a WHERE NULL clause.
     */
    public function whereNull(string $column): self
    {
        if (!empty($this->wheres)) {
            $this->wheres[] = 'AND';
        }

        $this->wheres[] = sprintf(
            '%s IS NULL',
            $column
        );

        return $this;
    }

    /**
     * Add a WHERE NOT NULL clause.
     */
    public function whereNotNull(string $column): self
    {
        if (!empty($this->wheres)) {
            $this->wheres[] = 'AND';
        }

        $this->wheres[] = sprintf(
            '%s IS NOT NULL',
            $column
        );

        return $this;
    }
}