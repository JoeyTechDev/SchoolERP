<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Relations;

use SchoolERP\ORM\Model;

final class HasOne extends Relation
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

    public function get(): ?Model
    {
        return $this->related
            ->query()
            ->where(
                $this->foreignKey,
                '=',
                $this->parent->{$this->localKey}
            )
            ->first();
    }
}