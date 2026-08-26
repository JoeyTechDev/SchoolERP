<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use PDOException;
use SchoolERP\Http\Response;
use SchoolERP\Http\Request;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Classroom Controller
 * --------------------------------------------------------------------------
 *
 * Handles classroom management.
 */
final class ClassroomController extends Controller
{
    /**
     * Classroom repository.
     */
    private ClassroomRepository $classrooms;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        ClassroomRepository $classrooms
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->classrooms = $classrooms;
    }

    /**
     * Display all classrooms.
     */
    public function index(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classrooms = $this->classrooms->allOrdered();

        return $this->view(
            'classrooms.index',
            [
                'title' => 'Classrooms',
                'classrooms' => $classrooms,
            ]
        );
    }

    /**
     * Show the create classroom form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        return $this->view(
            'classrooms.create',
            [
                'title' => 'Create Classroom',
            ]
        );
    }

    /**
     * Store a new classroom.
     */
    public function store(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $name = trim(
            (string) $request->input('name')
        );

        $data = [
            'name' => $name,
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:2|max:100',
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
                '/SchoolERP/public/classrooms/create'
            );
        }

        try {
            $this->classrooms->create(
                $data
            );
        } catch (PDOException $exception) {
            if ($this->isDuplicateNameException($exception)) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'name' => 'A classroom with this name already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/classrooms/create'
                );
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Classroom created successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/classrooms'
        );
    }

    /**
     * Show the edit classroom form.
     */
    public function edit(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classroom = $this->classrooms->find(
            $id
        );

        if ($classroom === null) {
            return Response::notFound();
        }

        return $this->view(
            'classrooms.edit',
            [
                'title' => 'Edit Classroom',
                'classroom' => $classroom,
            ]
        );
    }

    /**
     * Update a classroom.
     */
    public function update(
        Request $request,
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classroom = $this->classrooms->find(
            $id
        );

        if ($classroom === null) {
            return Response::notFound();
        }

        $name = trim(
            (string) $request->input('name')
        );

        $data = [
            'name' => $name,
        ];

        $validator = Validator::make(
            $data,
            [
                'name' => 'required|min:2|max:100',
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
                '/SchoolERP/public/classrooms/'
                . $id
                . '/edit'
            );
        }

        try {
            $updated = $this->classrooms->rename(
                $id,
                $name
            );
        } catch (PDOException $exception) {
            if ($this->isDuplicateNameException($exception)) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'name' => 'A classroom with this name already exists.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/classrooms/'
                    . $id
                    . '/edit'
                );
            }

            throw $exception;
        }

        if (!$updated) {
            $this->session->flash(
                'error',
                'Unable to update classroom.'
            );

            return $this->redirect(
                '/SchoolERP/public/classrooms/'
                . $id
                . '/edit'
            );
        }

        $this->session->flash(
            'success',
            'Classroom updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/classrooms'
        );
    }

    /**
     * Delete a classroom.
     */
    public function destroy(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classroom = $this->classrooms->find(
            $id
        );

        if ($classroom === null) {
            return Response::notFound();
        }

        if ($this->classrooms->hasStudents($id)) {
            $this->session->flash(
                'error',
                'This classroom cannot be deleted because students are still assigned to it.'
            );

            return $this->redirect(
                '/SchoolERP/public/classrooms'
            );
        }

        if (!$this->classrooms->delete($id)) {
            $this->session->flash(
                'error',
                'Unable to delete classroom.'
            );

            return $this->redirect(
                '/SchoolERP/public/classrooms'
            );
        }

        $this->session->flash(
            'success',
            'Classroom deleted successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/classrooms'
        );
    }

    /**
     * Determine whether an exception is a duplicate-key violation.
     */
    private function isDuplicateNameException(
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