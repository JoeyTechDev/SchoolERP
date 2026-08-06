<?php

declare(strict_types=1);

namespace SchoolERP\Http;

use SchoolERP\Session\SessionInterface;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Redirect Response
 * --------------------------------------------------------------------------
 *
 * Represents an HTTP redirect response.
 */
final class RedirectResponse extends Response
{
    /**
     * Session instance.
     */
    private ?SessionInterface $session = null;

    /**
     * Constructor.
     */
    public function __construct(
        string $url,
        int $status = 302
    ) {
        parent::__construct(
            '',
            $status,
            [
                'Location' => $url,
            ]
        );
    }

    /**
     * Attach the session.
     */
    public function session(
        SessionInterface $session
    ): self {

        $this->session = $session;

        return $this;
    }

    /**
     * Flash data to the session.
     */
    public function with(
        string $key,
        mixed $value
    ): self {

        if ($this->session !== null) {
            $this->session->flash(
                $key,
                $value
            );
        }

        return $this;
    }
}