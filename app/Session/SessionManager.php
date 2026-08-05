<?php

declare(strict_types=1);

namespace SchoolERP\Session;

use SchoolERP\Session\Flash\FlashBag;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Session Manager
 * --------------------------------------------------------------------------
 *
 * Native PHP session implementation.
 */
final class SessionManager implements SessionInterface
{    
    /**
     * Flash message manager.
     */
    private FlashBag $flash;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->flash = new FlashBag();
    }

    /**
     * Start the session.
     */
    public function start(): void
    {
        if (!$this->isStarted()) {
            session_start();
        }
    }

    /**
     * Determine whether the session is active.
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Store a value.
     */
    public function put(
        string $key,
        mixed $value
    ): void {

        $this->start();

        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a value.
     */
    public function get(
        string $key,
        mixed $default = null
    ): mixed {

        $this->start();

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Determine whether a key exists.
     */
    public function has(
        string $key
    ): bool {

        $this->start();

        return array_key_exists(
            $key,
            $_SESSION
        );
    }

    /**
     * Remove a value.
     */
    public function forget(
        string $key
    ): void {

        $this->start();

        unset($_SESSION[$key]);
    }

    /**
     * Get all session data.
     *
     * @return array<string,mixed>
     */
    public function all(): array
    {
        $this->start();

        return $_SESSION;
    }

    /**
     * Regenerate the session ID.
     */
    public function regenerate(): bool
    {
        $this->start();

        return session_regenerate_id(true);
    }

    /**
     * Destroy the session.
     */
    public function destroy(): bool
    {
        if (!$this->isStarted()) {
            return true;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        return session_destroy();
    }

    /**
     * Remove all session data.
     */
    public function flush(): void
    {
        $this->start();

        $_SESSION = [];
    }

        /**
     * Store or retrieve a flash message.
     */
    public function flash(
        string $key,
        mixed $value = null
    ): mixed {

        $this->start();

        if (func_num_args() === 2) {

            $this->flash->put(
                $key,
                $value
            );

            return null;
        }

        return $this->flash->get($key);
    }

    /**
     * Determine whether a flash message exists.
     */
    public function hasFlash(
        string $key
    ): bool {

        $this->start();

        return $this->flash->has($key);
    }

    /**
     * Remove all flash messages.
     */
    public function clearFlash(): void
    {
        $this->start();

        $this->flash->clear();
    }
}