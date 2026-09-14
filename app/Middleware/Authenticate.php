<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Models\User;
use SchoolERP\Services\AuthenticationService;
use SchoolERP\Session\SessionInterface;

final class Authenticate extends Middleware
{
    /**
     * Constructor.
     */
    public function __construct(
        private AuthenticationService $authentication,
        private SessionInterface $session,
        private User $users
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
        |--------------------------------------------------------------------------
        | Public authentication routes
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Require a valid authenticated session
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Verify the account still exists
        |--------------------------------------------------------------------------
        */
        $userId = (int) $this->session->get(
            'user_id',
            0
        );

        if ($userId <= 0) {
            $this->authentication->logout();

            $this->session->flash(
                '_auth_error',
                'Your session is no longer valid. Please log in again.'
            );

            return $this->redirect(
                '/SchoolERP/public/auth/login'
            );
        }

        $user = $this->users->find(
            $userId
        );

        if ($user === null) {
            $this->authentication->logout();

            $this->session->flash(
                '_auth_error',
                'Your account could not be found. Please log in again.'
            );

            return $this->redirect(
                '/SchoolERP/public/auth/login'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Check the live database account status
        |--------------------------------------------------------------------------
        |
        | This is important because an administrator can suspend or
        | deactivate an account while that user already has an active
        | browser session.
        |
        */
        $status = strtolower(
            trim(
                (string) (
                    $user->status ?? ''
                )
            )
        );

        if ($status !== 'active') {
            $this->authentication->logout();

            $message = match ($status) {
                'suspended' =>
                    'Your account has been suspended. Please contact the school administrator.',

                'inactive' =>
                    'Your account is inactive. Please contact the school administrator.',

                default =>
                    'Your account is not active. Please contact the school administrator.',
            };

            $this->session->flash(
                '_auth_error',
                $message
            );

            return $this->redirect(
                '/SchoolERP/public/auth/login'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Keep session status synchronized
        |--------------------------------------------------------------------------
        |
        | The database is authoritative. Keeping the session copy synchronized
        | prevents stale status information from remaining in the session.
        |
        */
        $this->session->put(
            'status',
            $status
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh session activity
        |--------------------------------------------------------------------------
        */
        $this->session->put(
            'last_activity',
            time()
        );

        /*
        |--------------------------------------------------------------------------
        | Validate browser fingerprint
        |--------------------------------------------------------------------------
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

        /*
        |--------------------------------------------------------------------------
        | Continue request
        |--------------------------------------------------------------------------
        */
        return $this->next(
            $request,
            $next
        );
    }
}