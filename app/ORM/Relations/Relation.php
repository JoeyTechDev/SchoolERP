<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Relations;

use SchoolERP\ORM\Model;
use SchoolERP\Query\QueryBuilder;

/**
 * Base relationship class.
 */
abstract class Relation
{
    /**
     * Parent model.
     */
    protected Model $parent;

    /**
     * Related model.
     */
    protected Model $related;

    /**
     * Query builder.
     */
    protected QueryBuilder $query;

    public function __construct(
        Model $parent,
        Model $related
    ) {
        $this->parent = $parent;

        $this->related = $related;

        $this->query = $related->getQuery();
    }

    /**
     * Execute the relationship query.
     */
    abstract public function get(): mixed;
}