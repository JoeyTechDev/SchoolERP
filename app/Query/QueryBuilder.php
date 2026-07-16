<?php

declare(strict_types=1);

namespace SchoolERP\Query;

use SchoolERP\Query\Concerns\BuildsSelectQueries;
use SchoolERP\Query\Concerns\BuildsInsertQueries;
use SchoolERP\Query\Concerns\BuildsUpdateQueries;
use SchoolERP\Query\Concerns\BuildsDeleteQueries;
use SchoolERP\Query\Concerns\ResetsBuilder;
use SchoolERP\Database\Database;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Query Builder
 * --------------------------------------------------------------------------
 *
 * Fluent SQL Query Builder.
 */
final class QueryBuilder
{    
    use BuildsSelectQueries;
    use BuildsInsertQueries;
    use BuildsUpdateQueries;
    use BuildsDeleteQueries;
    use ResetsBuilder;
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
     * ORDER BY clauses.
     *
     * @var array<int,string>
     */
    private array $orders = [];

    /**
     * Query limit.
     */
    private ?int $limit = null;

    /**
     * Create a Query Builder.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Set table.
     */
    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Select columns.
     *
     * @param array<int,string> $columns
     */
    public function select(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Add WHERE clause.
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
     * Add ORDER BY clause.
     */
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
     * Limit results.
     */
    public function limit(int $limit): self
    {
        $this->limit = max(0, $limit);

        return $this;
    }

    /**
     * Return the first matching record.
     *
     * @return array<string,mixed>|null
     */
    public function first(): ?array
    {
        $this->limit(1);

        $results = $this->get();

        return $results[0] ?? null;
    }

    /**
     * Get current table.
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
 * Insert a new record.
 *
 * @param array<string,mixed> $data
 */
public function insert(array $data): int
{
    if ($data === []) {
        throw new \InvalidArgumentException(
            'Insert data cannot be empty.'
        );
    }

    $columns = array_keys($data);

    $placeholders = implode(
        ', ',
        array_fill(0, count($columns), '?')
    );

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $this->table,
        implode(', ', $columns),
        $placeholders
    );

    $this->database->insert(
        $sql,
        array_values($data)
    );

    $id = (int) $this->database->lastInsertId();

    $this->reset();

    return $id;
    }

    /**
     * Execute query.
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

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        $results = $this->database->select(
            $sql,
            $this->bindings
        );

        $this->reset();

        return $results;
}

    /**
     * Reset builder state after query execution.
     */
    private function reset(): void
    {
        $this->table = '';

        $this->columns = ['*'];

        $this->wheres = [];

        $this->bindings = [];

        $this->orders = [];

        $this->limit = null;
    }
}