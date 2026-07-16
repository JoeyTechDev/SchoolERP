<?php

declare(strict_types=1);

namespace SchoolERP\Query;

use SchoolERP\Database\Database;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Query Builder
 * --------------------------------------------------------------------------
 *
 * Fluent SQL Query Builder.
 *
 * Responsibilities
 * ----------------
 * • Build SQL queries
 * • Execute queries
 * • Return results
 *
 * This class depends only on Database.
 */
final class QueryBuilder
{
    /**
     * Database manager.
     */
    private Database $database;

    /**
     * Current table.
     */
    private string $table = '';

    /**
     * Selected columns.
     *
     * @var array<int,string>
     */
    private array $columns = ['*'];

    /**
     * WHERE clauses.
     *
     * @var array<int,string>
     */
    private array $wheres = [];

    /**
     * Query bindings.
     *
     * @var array<int,mixed>
     */
    private array $bindings = [];

    /**
     * Create a Query Builder.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Set the table being queried.
     */
    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Specify the columns to select.
     *
     * @param array<int,string> $columns
     */
    public function select(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Add a WHERE clause.
     */
    public function where(
        string $column,
        string $operator,
        mixed $value
    ): self {

        $this->wheres[] = sprintf(
            '%s %s ?',
            $column,
            $operator
        );

        $this->bindings[] = $value;

        return $this;
    }

    /**
     * Get the current table.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Execute the query and return all results.
     *
     * @return array<int,array<string,mixed>>
     */
    public function get(): array
    {
        $columns = implode(', ', $this->columns);

        $sql = sprintf(
            'SELECT %s FROM %s',
            $columns,
            $this->table
        );

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        }

        return $this->database->select(
            $sql,
            $this->bindings
        );
    }
}