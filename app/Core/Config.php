<?php

declare(strict_types=1);

namespace SchoolERP\Core;

use InvalidArgumentException;

/**
 * ---------------------------------------------------------------
 * SchoolERP Configuration Service
 * ---------------------------------------------------------------
 *
 * Centralized configuration manager.
 *
 * - Loads PHP config arrays.
 * - Provides dot notation.
 * - Allows dependency injection.
 * - Future-ready for .env support.
 */

final class Config
{
    /**
     * Loaded configuration.
     *
     * @var array<string, mixed>
     */
    private array $items = [];

    /**
     * Load a configuration file.
     */
    public function load(string $name, array $config): void
    {
        $this->items[$name] = $config;
    }

    /**
     * Retrieve a configuration value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);

        $value = $this->items;

        foreach ($segments as $segment) {

            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Determine whether a configuration key exists.
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Override a configuration value.
     */
    public function set(string $key, mixed $value): void
    {
        $segments = explode('.', $key);

        $config = &$this->items;

        foreach ($segments as $segment) {

            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }

        $config = $value;
    }

    /**
     * Return all configuration.
     *
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->items;
    }
}