<?php

declare(strict_types=1);

namespace SchoolERP\Config;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Config
 * --------------------------------------------------------------------------
 *
 * Configuration Manager
 *
 * Responsibilities
 * ----------------
 * • Load configuration files
 * • Store configuration values
 * * Retrieve values
 * • Support dot notation
 *
 * This class is framework-independent.
 */
    final class Config
    {
    /**
     * Loaded configuration.
     *
     * @var array<string,mixed>
     */
    private array $items = [];

    /**
     * Configuration directory.
     */
    private string $configPath;

    /**
     * Create a Config instance.
     */
    public function __construct(
    string $configPath
    ) {
    $this->configPath = rtrim(
        $configPath,
        DIRECTORY_SEPARATOR
    );

    $this->loadConfiguration();
    }

/**
 * Load all configuration files.
 */
    private function loadConfiguration(): void
    {
    $files = glob(
        $this->configPath . DIRECTORY_SEPARATOR . '*.php'
    );

    if ($files === false) {
        return;
    }

    foreach ($files as $file) {

        $key = pathinfo(
            $file,
            PATHINFO_FILENAME
        );

        $this->items[$key] = require $file;
    }
    }

/**
 * Get a configuration value.
 */
    public function get(
    string $key,
    mixed $default = null
    ): mixed {

    $segments = explode('.', $key);

    $value = $this->items;

    foreach ($segments as $segment) {

        if (
            !is_array($value)
            || !array_key_exists($segment, $value)
        ) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
    }

}