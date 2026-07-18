<?php

declare(strict_types=1);

namespace SchoolERP\Query\Concerns;

/**
 * Join query methods.
 */
trait BuildsJoinQueries
{
    /**
     * Add an INNER JOIN clause.
     */
    public function join(
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {
        return $this->addJoin(
            'INNER',
            $table,
            $first,
            $operator,
            $second
        );
    }

    /**
     * Add a LEFT JOIN clause.
     */
    public function leftJoin(
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {
        return $this->addJoin(
            'LEFT',
            $table,
            $first,
            $operator,
            $second
        );
    }

    /**
     * Add a RIGHT JOIN clause.
     */
    public function rightJoin(
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {
        return $this->addJoin(
            'RIGHT',
            $table,
            $first,
            $operator,
            $second
        );
    }

    /**
     * Internal join builder.
     */
    private function addJoin(
        string $type,
        string $table,
        string $first,
        string $operator,
        string $second
    ): self {

        $this->joins[] = sprintf(
            '%s JOIN %s ON %s %s %s',
            strtoupper($type),
            $table,
            $first,
            $operator,
            $second
        );

        return $this;
    }
}