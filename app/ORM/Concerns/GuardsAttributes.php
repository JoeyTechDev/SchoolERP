<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * Handles mass assignment protection.
 */
trait GuardsAttributes
{
    /**
     * Fillable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [];

    /**
     * Guarded attributes.
     *
     * @var array<int,string>
     */
    protected array $guarded = ['*'];

    /**
     * Filter assignable attributes.
     *
     * @param array<string,mixed> $attributes
     *
     * @return array<string,mixed>
     */
    protected function filterFillable(array $attributes): array
    {
        /*
         * If fillable is defined,
         * only allow those attributes.
         */
        if ($this->fillable !== []) {

            return array_intersect_key(
                $attributes,
                array_flip($this->fillable)
            );
        }

        /*
         * If everything is guarded,
         * allow nothing.
         */
        if ($this->guarded === ['*']) {
            return [];
        }

        /*
         * Remove guarded fields.
         */
        foreach ($this->guarded as $column) {
            unset($attributes[$column]);
        }

        return $attributes;
    }
}