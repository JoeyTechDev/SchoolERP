<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Response;
use SchoolERP\Services\AuthenticationService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

final class DashboardController extends Controller
{
    /**
     * Authentication service.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        private AuthenticationService $authentication
    ) {
        parent::__construct(
            $views,
            $session
        );
    }

    /**
     * Display the authenticated dashboard.
     */
    public function index(): Response
    {
        return $this->view(
            'dashboard.index',
            [
                'title' => 'Dashboard',
                'user' => [
                    'id' => $this->authentication->userId(),
                    'role_id' => $this->authentication->roleId(),
                    'first_name' => $this->session->get(
                        'first_name',
                        ''
                    ),
                    'last_name' => $this->session->get(
                        'last_name',
                        ''
                    ),
                    'email' => $this->session->get(
                        'email',
                        ''
                    ),
                ],
            ]
        );
    }
}