<?php

declare(strict_types=1);

namespace SchoolERP\Security;

use SchoolERP\Session\SessionInterface;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * CSRF Protection
 * --------------------------------------------------------------------------
 *
 * Generates and validates CSRF tokens.
 */
final class Csrf
{
    /**
     * Session key.
     */
    private const SESSION_KEY = '_csrf_token';

    /**
     * Constructor.
     */
    public function __construct(
        private SessionInterface $session
    ) {
    }

    /**
     * Get the current token.
     */
    public function token(): string
    {
        if (!$this->session->has(self::SESSION_KEY)) {
            $this->session->put(
                self::SESSION_KEY,
                bin2hex(random_bytes(32))
            );
        }

        return (string) $this->session->get(
            self::SESSION_KEY
        );
    }

    /**
     * Generate a new token.
     */
    public function regenerate(): string
    {
        $token = bin2hex(
            random_bytes(32)
        );

        $this->session->put(
            self::SESSION_KEY,
            $token
        );

        return $token;
    }

    /**
     * Validate a submitted token.
     */
    public function verify(
        ?string $token
    ): bool {

        if ($token === null) {
            return false;
        }

        return hash_equals(
            $this->token(),
            $token
        );
    }
}