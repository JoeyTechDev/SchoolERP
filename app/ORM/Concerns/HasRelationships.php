<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

use SchoolERP\ORM\Model;
use SchoolERP\ORM\Relations\HasOne;
use SchoolERP\ORM\Relations\HasMany;
use SchoolERP\ORM\Relations\BelongsTo;

/**
 * Relationship methods for ORM models.
 */
trait HasRelationships
{
    /**
     * Define a Has One relationship.
     */
    protected function hasOne(
        string $related,
        string $foreignKey,
        string $localKey = 'id'
    ): HasOne {

        return new HasOne(
            $this,
            new $related(),
            $foreignKey,
            $localKey
        );
    }

    /**
     * Define a Has Many relationship.
     */
    protected function hasMany(
        string $related,
        string $foreignKey,
        string $localKey = 'id'
    ): HasMany {

        return new HasMany(
            $this,
            new $related(),
            $foreignKey,
            $localKey
        );
    }

    /**
     * Define a Belongs To relationship.
     */
    protected function belongsTo(
        string $related,
        string $foreignKey,
        string $ownerKey = 'id'
    ): BelongsTo {

        return new BelongsTo(
            $this,
            new $related(),
            $foreignKey,
            $ownerKey
        );
    }
}