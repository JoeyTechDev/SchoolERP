<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Services\AuthenticationService;
use SchoolERP\Session\SessionInterface;

final class Authenticate extends Middleware
{
    /**
     * Constructor.
     */
    public function __construct(
        private AuthenticationService $authentication,
        private SessionInterface $session
    ) {
    }

    /**
     * Handle the request.
     */
    public function handle(
        Request $request,
        callable $next
    ): Response {
        $path = $request->path();

        /*
         * Public authentication routes.
         */
        if (
            $path === '/auth/login'
            || $path === '/auth/logout'
        ) {
            return $this->next(
                $request,
                $next
            );
        }

        /*
         * Require authentication everywhere else.
         */
        if (!$this->authentication->check()) {
            $this->session->flash(
                '_auth_error',
                'Please log in to continue.'
            );

            return $this->redirect(
                '/SchoolERP/public/auth/login'
            );
        }

        /*
         * Refresh session activity.
         */
        $this->session->put(
            'last_activity',
            time()
        );

        /*
         * Validate the browser fingerprint.
         */
        $storedUserAgent = (string) $this->session->get(
            'user_agent',
            ''
        );

        $currentUserAgent = (string) (
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        );

        if (
            $storedUserAgent !== ''
            && $storedUserAgent !== $currentUserAgent
        ) {
            $this->authentication->logout();

            $this->session->flash(
                '_auth_error',
                'Your session has expired. Please log in again.'
            );

            return $this->redirect(
                '/SchoolERP/public/auth/login'
            );
        }

        return $this->next(
            $request,
            $next
        );
    }
}