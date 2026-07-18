<?php

declare(strict_types=1);

namespace SchoolERP\ORM;

use SchoolERP\ORM\Concerns\HasQueries;
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
     use HasQueries;
     
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

}