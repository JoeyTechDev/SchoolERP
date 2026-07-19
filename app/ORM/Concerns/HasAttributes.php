<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * Handles model attributes.
 */
trait HasAttributes
{
    /**
     * Model attributes.
     *
     * @var array<string,mixed>
     */
    protected array $attributes = [];

    /**
     * Original attributes from the database.
     *
     * @var array<string,mixed>
     */
    protected array $original = [];

    /**
     * Fill the model.
     *
     * @param array<string,mixed> $attributes
     */
    public function fill(array $attributes): static
    {
        $this->attributes = $attributes;

        $this->original = $attributes;

        return $this;
    }

    /**
     * Get all attributes.
     *
     * @return array<string,mixed>
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Magic getter.
     */
    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Magic setter.
     */
    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Determine if an attribute exists.
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

/**
 * Determine changed attributes.
 *
 * @return array<string,mixed>
 */
public function getDirty(): array
{
    $dirty = [];

    foreach ($this->attributes as $key => $value) {

        if (
            !array_key_exists($key, $this->original)
            || $this->original[$key] !== $value
        ) {
            $dirty[$key] = $value;
        }
    }

    return $dirty;
}

/**
 * Determine whether the model has changed.
 */
public function isDirty(): bool
{
    return $this->getDirty() !== [];
}
  
/**
 * Convert model to an array.
 *
 * @return array<string,mixed>
 */
public function toArray(): array
{
    return $this->attributes;
}
}