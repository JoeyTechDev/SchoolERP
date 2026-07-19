<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Relations;

use SchoolERP\ORM\Model;

final class BelongsTo extends Relation
{
    public function __construct(
        Model $child,
        Model $parent,
        protected string $foreignKey,
        protected string $ownerKey
    ) {
        parent::__construct(
            $child,
            $parent
        );
    }

    /**
     * Get the parent model.
     */
    public function get(): ?Model
    {
        return $this->related->find(
            $this->parent->{$this->foreignKey}
        );
    }
}