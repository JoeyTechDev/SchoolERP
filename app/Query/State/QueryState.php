<?php

declare(strict_types=1);

namespace SchoolERP\Query\State;

/**
 * Stores the mutable state of a query.
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

    /**
     * JOIN clauses.
     *
     * @var array<int,string>
     */
    public array $joins = [];

    /**
     * DISTINCT flag.
     */
    public bool $distinct = false;

    /**
     * Relationships to eager load.
     *
     * @var array<int,string>
     */
    public array $eagerLoads = [];
}