<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Services\AuthenticationService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;
use SchoolERP\Security\Csrf;

final class AuthController extends Controller
{
    /**
     * Authentication service.
     */
    private AuthenticationService $authentication;

    /**
     * CSRF service.
     */
    private Csrf $csrf;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        AuthenticationService $authentication,
        Csrf $csrf
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->authentication = $authentication;
        $this->csrf = $csrf;
    }

    /**
     * Show the login page.
     */
    public function showLogin(): Response
    {
        if ($this->authentication->check()) {
            return $this->redirect(
                '/SchoolERP/public/dashboard'
            );
        }

        return $this->view(
            'auth.login',
            [
                'title' => 'Login',
                'csrf_token' => $this->csrf->token(),
            ]
        );
    }

/**
 * Authenticate the user.
 */
public function login(
    Request $request
): Response {
    $email = trim(
        (string) $request->input('email')
    );

    $password = (string) $request->input(
        'password'
    );

    if ($email === '') {
        return $this->loginError(
            'Email is required.',
            $email
        );
    }

    if (!filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )) {
        return $this->loginError(
            'Please enter a valid email address.',
            $email
        );
    }

    if ($password === '') {
        return $this->loginError(
            'Password is required.',
            $email
        );
    }

    if (!$this->authentication->attempt(
        $email,
        $password
    )) {
        return $this->loginError(
            'Invalid email or password.',
            $email
        );
    }

    /*
     * Redirect according to the authenticated user's role.
     *
     * Role 1 = Administrator
     * Role 2 = Teacher
     * Role 3 = Student
     */
    $roleId =
        $this->authentication->roleId();

    return match ($roleId) {
        1 => $this->redirect(
            '/SchoolERP/public/dashboard'
        ),

        2 => $this->redirect(
            '/SchoolERP/public/teacher/dashboard'
        ),

        3 => $this->redirect(
            '/SchoolERP/public/student/dashboard'
        ),

        default => $this->redirect(
            '/SchoolERP/public/dashboard'
        ),
    };
}

    /**
     * Log the current user out.
     */
    public function logout(): Response
    {
        $this->authentication->logout();

        $this->session->flash(
            'success',
            'You have been logged out successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/auth/login'
        );
    }

    /**
     * Return to login with an error.
     */
    private function loginError(
        string $message,
        string $email = ''
    ): Response {
        $this->session->flash(
            '_auth_error',
            $message
        );

        $this->session->flash(
            '_old_login',
            [
                'email' => $email,
            ]
        );

        return $this->redirect(
            '/SchoolERP/public/auth/login'
        );
    }
}