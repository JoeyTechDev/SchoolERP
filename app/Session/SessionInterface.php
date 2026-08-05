<?php

declare(strict_types=1);

namespace SchoolERP\Session;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Session Interface
 * --------------------------------------------------------------------------
 *
 * Defines the session contract used throughout the framework.
 */
interface SessionInterface
{
    /**
     * Start the session.
     */
    public function start(): void;

    /**
     * Determine whether the session has started.
     */
    public function isStarted(): bool;

    /**
     * Store a value.
     */
    public function put(
        string $key,
        mixed $value
    ): void;

    /**
     * Retrieve a value.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed;

    /**
     * Determine whether a key exists.
     */
    public function has(
        string $key
    ): bool;

    /**
     * Remove a value.
     */
    public function forget(
        string $key
    ): void;

    /**
     * Get all session data.
     *
     * @return array<string,mixed>
     */
    public function all(): array;

    /**
     * Regenerate the session ID.
     */
    public function regenerate(): bool;

    /**
     * Destroy the session.
     */
    public function destroy(): bool;

    /**
     * Clear all session data.
     */
    public function flush(): void;

    /**
     * Store or retrieve a flash message.
     */
    public function flash(
        string $key,
        mixed $value = null
    ): mixed;

    /**
     * Determine whether a flash message exists.
     */
    public function hasFlash(
        string $key
    ): bool;

    /**
     * Remove all flash messages.
     */
    public function clearFlash(): void;
}