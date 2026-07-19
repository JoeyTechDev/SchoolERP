<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * Automatic timestamp handling.
 */
trait HasTimestamps
{
    /**
     * Add timestamps before insert.
     *
     * @param array<string,mixed> $attributes
     *
     * @return array<string,mixed>
     */
    protected function addCreationTimestamps(array $attributes): array
    {
        if (!$this->timestamps) {
            return $attributes;
        }

        $now = date('Y-m-d H:i:s');

        $attributes[$this->createdAtColumn] = $now;
        $attributes[$this->updatedAtColumn] = $now;

        return $attributes;
    }

    /**
     * Update the updated_at timestamp.
     */
    protected function touch(): void
    {
        if (!$this->timestamps) {
            return;
        }

        $this->attributes[$this->updatedAtColumn] =
            date('Y-m-d H:i:s');
    }
}