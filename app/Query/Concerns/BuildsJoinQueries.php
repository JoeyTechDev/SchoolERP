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

        $this->joins[] = sprintf(
            'INNER JOIN %s ON %s %s %s',
            $table,
            $first,
            $operator,
            $second
        );

        return $this;
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

        $this->joins[] = sprintf(
            'LEFT JOIN %s ON %s %s %s',
            $table,
            $first,
            $operator,
            $second
        );

        return $this;
    }
}