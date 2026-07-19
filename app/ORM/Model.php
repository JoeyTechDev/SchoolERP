<?php

declare(strict_types=1);

namespace SchoolERP\ORM;

use SchoolERP\ORM\Concerns\HasRelationships;
use SchoolERP\ORM\Concerns\HasCasts;
use SchoolERP\ORM\Concerns\GuardsAttributes;
use SchoolERP\ORM\Concerns\HasAttributes;
use SchoolERP\ORM\Concerns\HasTimestamps;
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
abstract class Model implements \JsonSerializable
{
    use HasCasts;
    use GuardsAttributes;
    use HasAttributes;
    use HasQueries;
    use HasTimestamps;
    use HasRelationships;
    

    /**
     * Database table.
     */
    protected string $table;

    /**
     * Enable automatic timestamps.
     */
    protected bool $timestamps = true;

    /**
     * Created timestamp column.
     */
    protected string $createdAtColumn = 'created_at';

    /**
     * Updated timestamp column.
     */
    protected string $updatedAtColumn = 'updated_at';

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
 * Get the Query Builder.
 */
public function getQuery(): QueryBuilder
{
    $this->query->table($this->table);

    return $this->query;
}

}