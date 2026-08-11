<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Relations;

use SchoolERP\ORM\Model;

/**
 * Has Many relationship.
 */
final class HasMany extends Relation
{
    /**
     * Create a Has Many relationship.
     */
    public function __construct(
        Model $parent,
        Model $related,
        protected string $foreignKey,
        protected string $localKey = 'id'
    ) {
        parent::__construct(
            $parent,
            $related
        );
    }

    /**
     * Get all related models.
     *
     * @return array<int,Model>
     */
    public function get(): array
    {
        $localValue = $this->parent->{$this->localKey};

        if ($localValue === null) {
            return [];
        }

        return $this->related
            ->where(
                $this->foreignKey,
                '=',
                $localValue
            )
            ->getModels();
    }
}
