<?php

declare(strict_types=1);

namespace SchoolERP\Query;

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
        $this->columns = $columns;

        return $this;
    }
    
/**
 * Add a WHERE clause.
 */
private function addWhere(
    string $boolean,
    string $column,
    string $operator,
    mixed $value
): self {

    if (!empty($this->wheres)) {
        $this->wheres[] = $boolean;
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
 * Add a WHERE clause.
 */
public function where(
    string $column,
    string $operator,
    mixed $value
): self {

    return $this->addWhere(
        'AND',
        $column,
        $operator,
        $value
    );
}

/**
 * Add an OR WHERE clause.
 */
public function orWhere(
    string $column,
    string $operator,
    mixed $value
): self {

    return $this->addWhere(
        'OR',
        $column,
        $operator,
        $value
    );
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
        throw new \InvalidArgumentException(
            'whereIn values cannot be empty.'
        );
    }

    $placeholders = implode(
        ', ',
        array_fill(0, count($values), '?')
    );

    if (!empty($this->wheres)) {
        $this->wheres[] = 'AND';
    }

    $this->wheres[] = sprintf(
        '%s IN (%s)',
        $column,
        $placeholders
    );

    foreach ($values as $value) {
        $this->bindings[] = $value;
    }

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
        throw new \InvalidArgumentException(
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
        $columns = implode(', ', $this->columns);

        $sql = sprintf(
            'SELECT %s FROM %s',
            $columns,
            $this->state->table,
        );

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . implode(' ', $this->wheres);
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
        $this->state->table = '';

        $this->columns = ['*'];

        $this->wheres = [];

        $this->bindings = [];

        $this->orders = [];

        $this->limit = null;
    }
/**
 * Current query state.
 */
private QueryState $state;
}