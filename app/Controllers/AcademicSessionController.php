<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use PDOException;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Academic Session Controller
 * --------------------------------------------------------------------------
 *
 * Handles academic session management.
 */
final class AcademicSessionController extends Controller
{
    /**
     * Academic session repository.
     */
    private AcademicSessionRepository $sessions;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        AcademicSessionRepository $sessions
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->sessions = $sessions;
    }

    /**
     * Display all academic sessions.
     */
    public function index(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        return $this->view(
            'academic-sessions.index',
            [
                'title' => 'Academic Sessions',
                'sessions' => $this->sessions->allOrdered(),
                'current' => $this->sessions->current(),
            ]
        );
    }

    /**
     * Show the create academic session form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        return $this->view(
            'academic-sessions.create',
            [
                'title' => 'Create Academic Session',
            ]
        );
    }

    /**
     * Store a new academic session.
     */
    public function store(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $data = [
            'name' => trim(
                (string) $request->input('name')
            ),
            'is_current' => 0,
            'status' => 'active',
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:7|max:20',
            ]
        );

        if ($validator->fails()) {
            $this->session->flash(
                '_old_input',
                $data
            );

            $this->session->flash(
                '_errors',
                $validator->errors()
            );

            return $this->redirect(
                '/SchoolERP/public/academic-sessions/create'
            );
        }

        try {
            $this->sessions->create($data);
        } catch (PDOException $exception) {
            if ($this->isDuplicateException($exception)) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'name' => 'An academic session with this name already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/academic-sessions/create'
                );
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Academic session created successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-sessions'
        );
    }

    /**
     * Show the edit academic session form.
     */
    public function edit(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $academicSession = $this->sessions->find($id);

        if ($academicSession === null) {
            return Response::notFound();
        }

        return $this->view(
            'academic-sessions.edit',
            [
                'title' => 'Edit Academic Session',
                'academicSession' => $academicSession,
            ]
        );
    }

    /**
     * Update an academic session.
     */
    public function update(
        Request $request,
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $academicSession = $this->sessions->find($id);

        if ($academicSession === null) {
            return Response::notFound();
        }

        $data = [
            'name' => trim(
                (string) $request->input('name')
            ),
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:7|max:20',
            ]
        );

        if ($validator->fails()) {
            $this->session->flash(
                '_old_input',
                $data
            );

            $this->session->flash(
                '_errors',
                $validator->errors()
            );

            return $this->redirect(
                '/SchoolERP/public/academic-sessions/'
                . $id
                . '/edit'
            );
        }

        try {
            $updated = $this->sessions->updateSession(
                $id,
                $data
            );
        } catch (PDOException $exception) {
            if ($this->isDuplicateException($exception)) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'name' => 'An academic session with this name already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/academic-sessions/'
                    . $id
                    . '/edit'
                );
            }

            throw $exception;
        }

        if (!$updated) {
            $this->session->flash(
                'error',
                'Unable to update academic session.'
            );

            return $this->redirect(
                '/SchoolERP/public/academic-sessions/'
                . $id
                . '/edit'
            );
        }

        $this->session->flash(
            'success',
            'Academic session updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-sessions'
        );
    }

    /**
     * Set an academic session as current.
     */
    public function setCurrent(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $academicSession = $this->sessions->find($id);

        if ($academicSession === null) {
            return Response::notFound();
        }

        if (!$this->sessions->setCurrent($id)) {
            $this->session->flash(
                'error',
                'Unable to set the academic session as current.'
            );

            return $this->redirect(
                '/SchoolERP/public/academic-sessions'
            );
        }

        $this->session->flash(
            'success',
            'Academic session is now the current session.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-sessions'
        );
    }

    /**
     * Activate an academic session.
     */
    public function activate(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        if ($this->sessions->find($id) === null) {
            return Response::notFound();
        }

        if (!$this->sessions->activate($id)) {
            $this->session->flash(
                'error',
                'Unable to activate academic session.'
            );

            return $this->redirect(
                '/SchoolERP/public/academic-sessions'
            );
        }

        $this->session->flash(
            'success',
            'Academic session activated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-sessions'
        );
    }

    /**
     * Deactivate an academic session.
     */
    public function deactivate(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        if ($this->sessions->find($id) === null) {
            return Response::notFound();
        }

        if (!$this->sessions->deactivate($id)) {
            $this->session->flash(
                'error',
                'The current academic session cannot be deactivated.'
            );

            return $this->redirect(
                '/SchoolERP/public/academic-sessions'
            );
        }

        $this->session->flash(
            'success',
            'Academic session deactivated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-sessions'
        );
    }

    /**
     * Determine whether an exception is a duplicate-key violation.
     */
    private function isDuplicateException(
        PDOException $exception
    ): bool {
        return $exception->getCode() === '23000'
            || str_contains(
                strtolower(
                    $exception->getMessage()
                ),
                'duplicate'
            );
    }
}