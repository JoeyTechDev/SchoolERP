<?php

declare(strict_types=1);

namespace SchoolERP\Query\State;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Query State
 * --------------------------------------------------------------------------
 *
 * Stores the current state of a query being built.
 */
final class QueryState
{
    /**
     * Table name.
     */
    public string $table = '';

    /**
     * Selected columns.
     *
     * @var array<int,string>
     */
    public array $columns = ['*'];

    /**
     * WHERE clauses.
     *
     * @var array<int,string>
     */
    public array $wheres = [];

    /**
     * Query bindings.
     *
     * @var array<int,mixed>
     */
    public array $bindings = [];

    /**
     * ORDER BY clauses.
     *
     * @var array<int,string>
     */
    public array $orders = [];

    /**
     * LIMIT value.
     */
    public ?int $limit = null;
}