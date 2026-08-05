<?php

declare(strict_types=1);

namespace SchoolERP\Session\Flash;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Flash Bag
 * --------------------------------------------------------------------------
 *
 * Stores flash messages for one request.
 */
final class FlashBag
{
    /**
     * Session key.
     */
    private const KEY = '__flash';

    /**
     * Store a flash message.
     */
    public function put(
        string $key,
        mixed $value
    ): void {

        $_SESSION[self::KEY][$key] = $value;
    }

    /**
     * Get and remove a flash message.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {

        if (!$this->has($key)) {
            return $default;
        }

        $value = $_SESSION[self::KEY][$key];

        unset($_SESSION[self::KEY][$key]);

        return $value;
    }

    /**
     * Determine whether a flash message exists.
     */
    public function has(
        string $key
    ): bool {

        return isset(
            $_SESSION[self::KEY][$key]
        );
    }

    /**
     * Remove every flash message.
     */
    public function clear(): void
    {
        unset($_SESSION[self::KEY]);
    }

    /**
     * Return all flash messages.
     *
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $_SESSION[self::KEY] ?? [];
    }
}