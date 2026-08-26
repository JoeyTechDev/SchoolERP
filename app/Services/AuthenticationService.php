<?php

declare(strict_types=1);

namespace SchoolERP\Services;

use SchoolERP\Repositories\UserRepository;
use SchoolERP\Session\SessionInterface;

final class AuthenticationService
{
    /**
     * User repository.
     */
    public function __construct(
        private UserRepository $users,
        private SessionInterface $session
    ) {
    }

    /**
     * Attempt to authenticate a user.
     */
    public function attempt(
        string $email,
        string $password
    ): bool {
        $email = trim($email);

        if ($email === '' || $password === '') {
            return false;
        }

        $user = $this->users->findActiveByEmail(
            $email
        );

        if ($user === null) {
            return false;
        }

        if (!password_verify(
            $password,
            (string) $user->password
        )) {
            return false;
        }

        /*
         * Prevent session fixation.
         */
        $this->session->regenerate();

        /*
         * Store authenticated user data.
         */
        $this->session->put(
            'user_id',
            (int) $user->id
        );

        $this->session->put(
            'role_id',
            (int) $user->role_id
        );

        $this->session->put(
            'first_name',
            (string) $user->first_name
        );

        $this->session->put(
            'last_name',
            (string) $user->last_name
        );

        $this->session->put(
            'email',
            (string) $user->email
        );

        $this->session->put(
            'status',
            (string) $user->status
        );

        $this->session->put(
            'login_time',
            time()
        );

        $this->session->put(
            'last_activity',
            time()
        );

        $this->session->put(
            'user_agent',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        );

        /*
         * Update last login.
         */
        $this->users->updateLastLogin(
            (int) $user->id
        );

        return true;
    }

    /**
     * Determine whether a user is authenticated.
     */
    public function check(): bool
    {
        return $this->session->has('user_id');
    }

/**
 * Log the current user out.
 */
public function logout(): void
{
    if (!$this->check()) {
        return;
    }

    /*
     * Remove authentication state.
     */
    $this->session->forget('user_id');
    $this->session->forget('role_id');
    $this->session->forget('first_name');
    $this->session->forget('last_name');
    $this->session->forget('email');
    $this->session->forget('status');
    $this->session->forget('login_time');
    $this->session->forget('last_activity');
    $this->session->forget('user_agent');

    /*
     * Regenerate the session ID to prevent session fixation
     * and invalidate the previous authenticated session ID.
     */
    $this->session->regenerate();
}

    /**
     * Get the current authenticated user ID.
     */
    public function userId(): ?int
    {
        if (!$this->check()) {
            return null;
        }

        return (int) $this->session->get(
            'user_id'
        );
    }

    /**
     * Get the current authenticated role ID.
     */
    public function roleId(): ?int
    {
        if (!$this->check()) {
            return null;
        }

        return (int) $this->session->get(
            'role_id'
        );
    }
}