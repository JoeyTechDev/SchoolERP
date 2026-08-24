<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Relations;

use SchoolERP\ORM\Model;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Belongs To Relationship
 * --------------------------------------------------------------------------
 *
 * Represents a child model belonging to a parent model.
 */
final class BelongsTo extends Relation
{
    /**
     * Create a Belongs To relationship.
     */
    public function __construct(
        Model $child,
        Model $parent,
        protected string $foreignKey,
        protected string $ownerKey = 'id'
    ) {
        parent::__construct(
            $child,
            $parent
        );
    }

    /**
     * Get the related parent model.
     */
    public function get(): ?Model
    {
        $foreignValue = $this->parent->{$this->foreignKey};

        if ($foreignValue === null) {
            return null;
        }

        return $this->related
            ->where(
                $this->ownerKey,
                '=',
                $foreignValue
            )
            ->first();
    }
}