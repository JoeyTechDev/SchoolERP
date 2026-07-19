<?php

declare(strict_types=1);

namespace SchoolERP\Query;

use SchoolERP\Query\Pagination\Paginator;
use SchoolERP\Query\Concerns\BuildsJoinQueries;
use SchoolERP\Query\Concerns\BuildsAggregateQueries;
use SchoolERP\Query\Concerns\BuildsWhereClauses;
use SchoolERP\Query\State\QueryState;
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
    use BuildsJoinQueries;
    use BuildsAggregateQueries;
    use BuildsWhereClauses; 
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
     * Query offset.
     */
    private ?int $offset = null;

    /**
     * JOIN clauses.
     *
     * @var array<int,string>
     */
    private array $joins = [];

    /**
     * DISTINCT flag.
     */
    private bool $distinct = false;

/**
 * Relationships to eager load.
 *
 * @var array<int,string>
 */
private array $eagerLoads = [];

    /**
     * Create a Query Builder.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
        
        $this->state = new QueryState();
    }

    /**
     * Set table.
     */
    public function table(string $table): self
    {
        $this->state->table = $table;

        return $this;
    }

    /**
     * Select columns.
     *
     * @param array<int,string> $columns
     */
    public function select(array $columns): self
    {
        $this->state->columns = $columns;

        return $this;
    }
    
    /**
     * Select distinct records.
     */
    public function distinct(): self
    {
        $this->distinct = true;

        return $this;
    }

    /**
 * Eager load relationships.
 *
 * @param string|array<int,string> $relations
 */
public function with(
    string|array $relations
): self {

    if (is_string($relations)) {
        $relations = [$relations];
    }

    $this->eagerLoads = array_merge(
        $this->eagerLoads,
        $relations
    );

    return $this;
}

/**
 * Get eager-loaded relationships.
 *
 * @return array<int,string>
 */
public function getEagerLoads(): array
{
    return $this->eagerLoads;
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
     * Offset results.
     */
    public function offset(int $offset): self
    {
        $this->offset = max(0, $offset);

        return $this;
    }

    /**
     * Paginate the query results.
     */
    public function paginate(
        int $perPage = 15,
        int $page = 1
    ): Paginator {

        $page = max(1, $page);

        $perPage = max(1, $perPage);

    /*
     * Count total records before applying
     * LIMIT and OFFSET.
     */
    $total = $this->count();

    /*
     * Calculate offset.
     */
    $offset = ($page - 1) * $perPage;

    /*
     * Retrieve current page.
     */
    $items = $this
        ->limit($perPage)
        ->offset($offset)
        ->get();

    return new Paginator(
        $items,
        $total,
        $perPage,
        $page
    );
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
    return $this->state->table;
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
    $this->state->table,
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
 * Update existing records.
 *
 * @param array<string,mixed> $data
 */
public function update(array $data): int
{
    if ($data === []) {
        throw new \InvalidArgumentException(
            'Update data cannot be empty.'
        );
    }

    $set = [];

    foreach ($data as $column => $value) {
        $set[] = "{$column} = ?";
    }

    $sql = sprintf(
        'UPDATE %s SET %s',
        $this->state->table,
        implode(', ', $set)
    );

    if (!empty($this->wheres)) {
        $sql .= ' WHERE ' . implode(' ', $this->wheres);
    }

    $bindings = array_merge(
        array_values($data),
        $this->bindings
    );

    $affected = $this->database->update(
        $sql,
        $bindings
    );

    $this->reset();

    return $affected;
    }
    
/**
 * Delete records.
 */
public function delete(): int
{
    if (empty($this->wheres)) {
        throw new \RuntimeException(
            'Refusing to delete records without a WHERE clause.'
        );
    }

    $sql = sprintf(
        'DELETE FROM %s',
        $this->state->table
    );

    $sql .= ' WHERE ' . implode(' ', $this->wheres
    );

    $affected = $this->database->delete(
        $sql,
        $this->bindings
    );

    $this->reset();

    return $affected;
}
    /**
     * Execute query.
     *
     * @return array<int,array<string,mixed>>
     */
    public function get(): array
    {
        $columns = implode(', ', $this->state->columns);

        $sql = sprintf(
    'SELECT %s%s FROM %s',
    $this->distinct ? 'DISTINCT ' : '',
    $columns,
    $this->state->table
    );
    
        if (!empty($this->joins)) {
        $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' ', $this->wheres);
        }

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {

        $sql .= ' LIMIT ' . $this->limit;

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }
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
        $this->state = new QueryState();

        $this->state->columns = ['*'];

        $this->wheres = [];

        $this->bindings = [];

        $this->orders = [];
        
        $this->limit = null;

        $this->offset = null;

        $this->joins = [];

        $this->distinct = false;

        $this->eagerLoads = [];
    }
/**
 * Current query state.
 */
private QueryState $state;
}