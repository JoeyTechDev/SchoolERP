<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Relations;

use SchoolERP\ORM\Model;

/**
 * Belongs To relationship.
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

        if ($this->ownerKey === 'id') {
            return $this->related->find(
                (int) $foreignValue
            );
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
