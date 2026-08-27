<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use PDOException;
use SchoolERP\Controllers\Controller;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

final class SubjectController extends Controller
{
    /**
     * Subject repository.
     */
    private SubjectRepository $subjects;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        SubjectRepository $subjects
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->subjects = $subjects;
    }

    /**
     * Display all subjects.
     */
    public function index(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $subjects = $this->subjects->allOrdered();

        return $this->view(
            'subjects.index',
            [
                'title' => 'Subjects',
                'subjects' => $subjects,
            ]
        );
    }

    /**
     * Show the create subject form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        return $this->view(
            'subjects.create',
            [
                'title' => 'Create Subject',
            ]
        );
    }

    /**
     * Store a new subject.
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

            'code' => trim(
                (string) $request->input('code')
            ),

            'description' => trim(
                (string) $request->input('description')
            ),

            'status' => $request->input('status') === 'inactive'
                ? 'inactive'
                : 'active',
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:2|max:100',
                'code' => 'nullable|max:30',
                'description' => 'nullable|max:65535',
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
                '/SchoolERP/public/subjects/create'
            );
        }

        try {
            $this->subjects->create($data);
        } catch (PDOException $exception) {
            if ($this->isDuplicateException($exception)) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'name' => 'A subject with this name or code already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/subjects/create'
                );
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Subject created successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/subjects'
        );
    }

    /**
     * Show the edit subject form.
     */
    public function edit(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $subject = $this->subjects->find($id);

        if ($subject === null) {
            return Response::notFound();
        }

        return $this->view(
            'subjects.edit',
            [
                'title' => 'Edit Subject',
                'subject' => $subject,
            ]
        );
    }

    /**
     * Update a subject.
     */
    public function update(
        Request $request,
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $subject = $this->subjects->find($id);

        if ($subject === null) {
            return Response::notFound();
        }

        $data = [
            'name' => trim(
                (string) $request->input('name')
            ),

            'code' => trim(
                (string) $request->input('code')
            ),

            'description' => trim(
                (string) $request->input('description')
            ),

            'status' => $request->input('status') === 'inactive'
                ? 'inactive'
                : 'active',
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:2|max:100',
                'code' => 'nullable|max:30',
                'description' => 'nullable|max:65535',
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
                '/SchoolERP/public/subjects/'
                . $id
                . '/edit'
            );
        }

        try {
            $updated = $this->subjects->updateSubject(
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
                        'name' => 'A subject with this name or code already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/subjects/'
                    . $id
                    . '/edit'
                );
            }

            throw $exception;
        }

        if (!$updated) {
            $this->session->flash(
                'error',
                'Unable to update subject.'
            );

            return $this->redirect(
                '/SchoolERP/public/subjects/'
                . $id
                . '/edit'
            );
        }

        $this->session->flash(
            'success',
            'Subject updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/subjects'
        );
    }

    /**
     * Activate a subject.
     */
    public function activate(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        if ($this->subjects->find($id) === null) {
            return Response::notFound();
        }

        if (!$this->subjects->activate($id)) {
            $this->session->flash(
                'error',
                'Unable to activate subject.'
            );

            return $this->redirect(
                '/SchoolERP/public/subjects'
            );
        }

        $this->session->flash(
            'success',
            'Subject activated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/subjects'
        );
    }

    /**
     * Deactivate a subject.
     */
    public function deactivate(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        if ($this->subjects->find($id) === null) {
            return Response::notFound();
        }

        if (!$this->subjects->deactivate($id)) {
            $this->session->flash(
                'error',
                'Unable to deactivate subject.'
            );

            return $this->redirect(
                '/SchoolERP/public/subjects'
            );
        }

        $this->session->flash(
            'success',
            'Subject deactivated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/subjects'
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