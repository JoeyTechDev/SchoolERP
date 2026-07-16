<?php

declare(strict_types=1);

namespace SchoolERP\Query;

use SchoolERP\Database\Database;

final class QueryBuilder
{
    private Database $database;

    private string $table = '';

    /** @var array<int,string> */
    private array $columns = ['*'];

    /** @var array<int,string> */
    private array $wheres = [];

    /** @var array<int,mixed> */
    private array $bindings = [];

    /** @var array<int,string> */
    private array $orders = [];

    private ?int $limit = null;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function select(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

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

    public function orderBy(
        string $column,
        string $direction = 'ASC'
    ): self {

        $direction = strtoupper($direction);

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }

        $this->orders[] = sprintf(
            '%s %s',
            $column,
            $direction
        );

        return $this;
    }

    /**
     * Limit returned rows.
     */
    public function limit(int $limit): self
    {
        $this->limit = max(0, $limit);

        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    /**
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

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        return $this->database->select(
            $sql,
            $this->bindings
        );
    }
}