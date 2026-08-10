<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Guards Attributes
 * --------------------------------------------------------------------------
 *
 * Handles mass-assignment protection for ORM models.
 */
trait GuardsAttributes
{
    /**
     * Attributes explicitly allowed for mass assignment.
     *
     * @var array<int,string>
     */
    protected array $fillable = [];

    /**
     * Attributes explicitly protected from mass assignment.
     *
     * @var array<int,string>
     */
    protected array $guarded = ['*'];

    /**
     * Determine whether the model has an explicit fillable list.
     */
    public function isFillable(string $key): bool
    {
        /*
         * Explicitly fillable attributes always win.
         */
        if (in_array($key, $this->fillable, true)) {
            return true;
        }

        /*
         * If the model is completely guarded,
         * nothing else may be mass assigned.
         */
        if (in_array('*', $this->guarded, true)) {
            return false;
        }

        /*
         * Attributes listed in guarded are rejected.
         */
        if (in_array($key, $this->guarded, true)) {
            return false;
        }

        /*
         * If there is no wildcard guard and the attribute
         * is not explicitly guarded, allow it.
         */
        return true;
    }

    /**
     * Filter attributes for mass assignment.
     *
     * @param array<string,mixed> $attributes
     *
     * @return array<string,mixed>
     */
    public function filterFillable(
        array $attributes
    ): array {
        $filtered = [];

        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Filter attributes using the model's fillable rules.
     *
     * This method is retained as the public mass-assignment
     * entry point used by HasQueries::create().
     *
     * @param array<string,mixed> $attributes
     *
     * @return array<string,mixed>
     */
    public function fillable(
        array $attributes
    ): array {
        return $this->filterFillable($attributes);
    }

    /**
     * Set the fillable attributes.
     *
     * @param array<int,string> $attributes
     */
    public function setFillable(
        array $attributes
    ): static {
        $this->fillable = array_values($attributes);

        return $this;
    }

    /**
     * Get the fillable attributes.
     *
     * @return array<int,string>
     */
    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * Set the guarded attributes.
     *
     * @param array<int,string> $attributes
     */
    public function setGuarded(
        array $attributes
    ): static {
        $this->guarded = array_values($attributes);

        return $this;
    }

    /**
     * Get the guarded attributes.
     *
     * @return array<int,string>
     */
    public function getGuarded(): array
    {
        return $this->guarded;
    }
}
