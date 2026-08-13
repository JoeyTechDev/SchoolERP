<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Student Controller
 * --------------------------------------------------------------------------
 */
final class StudentController extends Controller
{
    /**
     * Student repository.
     */
    private StudentRepository $students;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        StudentRepository $students
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->students = $students;
    }

    /**
     * Display all students.
     */
    public function index(
        Request $request
    ): Response {
        $page = max(
            1,
            (int) $request->get('page', 1)
        );

        $pagination = $this->students->paginate(
            $page,
            10
        );

        return $this->view(
            'students.index',
            [
                'students' => $pagination->items(),
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * Display a single student.
     */
    public function show(
        int $id
    ): Response {
        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

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
        return $this->view(
            'students.create',
            [
                'title' => 'Create Student',
            ]
        );
    }

    /**
     * Store a new student.
     */
    public function store(
        Request $request
    ): Response {
        $this->students->create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'classroom_id' => $request->input('classroom_id'),
        ]);

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
        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

        return $this->view(
            'students.edit',
            [
                'title' => 'Edit Student',
                'student' => $student,
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
        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

        $this->students->update(
            $id,
            [
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'classroom_id' => $request->input('classroom_id'),
            ]
        );

        return $this->redirect(
            '/SchoolERP/public/students/' . $id
        );
    }

    /**
     * Delete a student.
     */
    public function destroy(
        int $id
    ): Response {
        $student = $this->students->find($id);

        if ($student === null) {
            return Response::notFound();
        }

        $this->students->delete($id);

        return $this->redirect(
            '/SchoolERP/public/students'
        );
    }
}