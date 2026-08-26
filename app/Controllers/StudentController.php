<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Student Controller
 * --------------------------------------------------------------------------
 *
 * Handles student management.
 */
final class StudentController extends Controller
{
    /**
     * Student repository.
     */
    private StudentRepository $students;

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
        StudentRepository $students,
        ClassroomRepository $classrooms
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->students = $students;
        $this->classrooms = $classrooms;
    }

    /**
     * Display students.
     *
     * Administrators and teachers can access student management.
     */
    public function index(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $page = max(
            1,
            (int) $request->get('page', 1)
        );

        $search = trim(
            (string) $request->get('q', '')
        );

        if ($search !== '') {
            $pagination = $this->students->searchPaginated(
                $search,
                $page,
                10
            );
        } else {
            $pagination = $this->students->paginate(
                $page,
                10
            );
        }

        return $this->view(
            'students.index',
            [
                'students' => $pagination->items(),
                'pagination' => $pagination,
                'search' => $search,
            ]
        );
    }

    /**
     * Display a single student.
     */
    public function show(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

        /*
         * Load the classroom relationship.
         */
        $student->setRelation(
            'classroom',
            $student->classroom()->get()
        );

        return $this->view(
            'students.show',
            [
                'student' => $student,
            ]
        );
    }

    /**
     * Show the create student form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classrooms = $this->classrooms->allOrdered();

        return $this->view(
            'students.create',
            [
                'title' => 'Create Student',
                'classrooms' => $classrooms,
            ]
        );
    }

    /**
     * Store a new student.
     */
    public function store(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classroomId = $request->input(
            'classroom_id'
        );

        $data = [
            'first_name' => trim(
                (string) $request->input('first_name')
            ),

            'last_name' => trim(
                (string) $request->input('last_name')
            ),

            'classroom_id' => $classroomId === null
                || trim((string) $classroomId) === ''
                ? null
                : (string) $classroomId,
        ];

        $validator = Validator::make(
            $data,
            [
                'first_name' => 'required|min:2|max:100',
                'last_name' => 'required|min:2|max:100',
                'classroom_id' => 'nullable|integer|min:1',
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
                '/SchoolERP/public/students/create'
            );
        }

        $this->students->create(
            $data
        );

        $this->session->flash(
            'success',
            'Student created successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/students'
        );
    }

    /**
     * Show the edit student form.
     */
    public function edit(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

        $classrooms = $this->classrooms->allOrdered();

        return $this->view(
            'students.edit',
            [
                'title' => 'Edit Student',
                'student' => $student,
                'classrooms' => $classrooms,
            ]
        );
    }

    /**
     * Update an existing student.
     */
    public function update(
        Request $request,
        int $id
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

        $classroomId = $request->input(
            'classroom_id'
        );

        $data = [
            'first_name' => trim(
                (string) $request->input('first_name')
            ),

            'last_name' => trim(
                (string) $request->input('last_name')
            ),

            'classroom_id' => $classroomId === null
                || trim((string) $classroomId) === ''
                ? null
                : (string) $classroomId,
        ];

        $validator = Validator::make(
            $data,
            [
                'first_name' => 'required|min:2|max:100',
                'last_name' => 'required|min:2|max:100',
                'classroom_id' => 'nullable|integer|min:1',
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
                '/SchoolERP/public/students/'
                . $id
                . '/edit'
            );
        }

        $updated = $this->students->update(
            $id,
            $data
        );

        if (!$updated) {
            $this->session->flash(
                'error',
                'Unable to update student.'
            );

            return $this->redirect(
                '/SchoolERP/public/students/'
                . $id
                . '/edit'
            );
        }

        $this->session->flash(
            'success',
            'Student updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/students/'
            . $id
        );
    }

    /**
     * Delete a student.
     */
    public function destroy(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

        if (!$this->students->delete($id)) {
            $this->session->flash(
                'error',
                'Unable to delete student.'
            );

            return $this->redirect(
                '/SchoolERP/public/students'
            );
        }

        $this->session->flash(
            'success',
            'Student deleted successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/students'
        );
    }
}