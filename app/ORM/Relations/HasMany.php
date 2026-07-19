<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Relations;

use SchoolERP\ORM\Model;

final class HasMany extends Relation
{
    public function __construct(
        Model $parent,
        Model $related,
        protected string $foreignKey,
        protected string $localKey
    ) {
        parent::__construct(
            $parent,
            $related
        );
    }

    /**
     * Get all related models.
     *
     * @return array<int,object>
     */
    public function get(): array
    {
        return $this->related
            ->query()
            ->where(
                $this->foreignKey,
                '=',
                $this->parent->{$this->localKey}
            )
            ->get();
    }
}