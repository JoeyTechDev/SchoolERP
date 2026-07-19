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
     * Original database attributes.
     *
     * @var array<string,mixed>
     */
    protected array $original = [];

    /**
     * Fill the model with attributes.
     *
     * @param array<string,mixed> $attributes
     */
    public function fill(array $attributes): static
    {
        $this->attributes = [];

        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $this->castAttribute(
                $key,
                $value
            );
        }

        $this->original = $this->attributes;

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
     * Get an attribute.
     */
    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set an attribute.
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
        return !empty($this->getDirty());
    }

    /**
     * Convert model to an array.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->attributes as $key => $value) {

            if ($value instanceof \DateTimeInterface) {
                $array[$key] = $value->format('Y-m-d H:i:s');
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * Convert model to JSON.
     */
    public function toJson(
        int $options = JSON_PRETTY_PRINT
    ): string {

        $json = json_encode(
            $this->toArray(),
            $options
        );

        if ($json === false) {
            throw new \RuntimeException(
                json_last_error_msg()
            );
        }

        return $json;
    }

    /**
     * Allow json_encode($model).
     *
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}