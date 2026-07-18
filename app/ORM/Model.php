<?php

declare(strict_types=1);

namespace SchoolERP\ORM;

use SchoolERP\Config\Config;
use SchoolERP\Database\Database;
use SchoolERP\Query\QueryBuilder;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Base ORM Model
 * --------------------------------------------------------------------------
 *
 * Parent model for all application models.
 */
abstract class Model
{
    /**
     * Database table.
     */
    protected string $table;

    /**
     * Query Builder instance.
     */
    protected QueryBuilder $query;

    /**
     * Database instance.
     */
    protected Database $database;

    /**
     * Configuration instance.
     */
    protected Config $config;

    /**
     * Create a model instance.
     */
    public function __construct()
    {
        $this->config = new Config(
            dirname(__DIR__, 2) . '/config'
        );

        $this->database = new Database(
            $this->config
        );

        $this->query = new QueryBuilder(
            $this->database
        );
    }

    /**
     * Get all records.
     *
     * @return array<int,array<string,mixed>>
     */
    public function all(): array
    {
        return $this->query
            ->table($this->table)
            ->select(['*'])
            ->get();
    }

    /**
     * Find a record by ID.
     *
     * @return array<string,mixed>|null
     */
    public function find(int $id): ?array
    {
        return $this->query
            ->table($this->table)
            ->where('id', '=', $id)
            ->first();
    }
}