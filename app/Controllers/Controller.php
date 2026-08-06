<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Response;
use SchoolERP\Http\RedirectResponse;
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
     * JSON response.
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
     * Redirect response.
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
     * Plain response.
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
}