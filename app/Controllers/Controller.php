<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\RedirectResponse;
use SchoolERP\Http\Response;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Base Controller
 * --------------------------------------------------------------------------
 *
 * Base class for all controllers.
 */
abstract class Controller
{
    /**
     * View factory.
     */
    protected ViewFactory $views;

    /**
     * Session manager.
     */
    protected SessionInterface $session;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session
    ) {
        $this->views = $views;
        $this->session = $session;
    }

    /**
     * Render a view.
     *
     * @param array<string,mixed> $data
     */
    protected function view(
        string $view,
        array $data = []
    ): Response {
        $html = $this->views
            ->layout('app')
            ->make($view, $data)
            ->render();

        return Response::make($html);
    }

    /**
     * Return a JSON response.
     *
     * @param array<string,mixed> $data
     */
    protected function json(
        array $data,
        int $status = 200
    ): Response {
        return Response::json(
            $data,
            $status
        );
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(
        string $url,
        int $status = 302
    ): RedirectResponse {
        return (new RedirectResponse(
            $url,
            $status
        ))->session(
            $this->session
        );
    }

    /**
     * Return a plain response.
     */
    protected function response(
        string $content,
        int $status = 200
    ): Response {
        return Response::make(
            $content,
            $status
        );
    }

    /**
     * Determine whether the current user has one of the supplied roles.
     *
     * @param array<int,int> $roles
     */
    protected function hasAnyRole(
        array $roles
    ): bool {
        if (!$this->session->has('user_id')) {
            return false;
        }

        $roleId = (int) $this->session->get(
            'role_id',
            0
        );

        return in_array(
            $roleId,
            $roles,
            true
        );
    }

    /**
     * Require one of the supplied roles.
     *
     * Returns a 403 response when authorization fails.
     *
     * @param array<int,int> $roles
     */
    protected function requireRole(
        array $roles
    ): ?Response {
        if ($this->hasAnyRole($roles)) {
            return null;
        }

        return Response::make(
            '403 Forbidden - You do not have permission to access this page.',
            403
        );
    }
}