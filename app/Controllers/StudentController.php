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

        $page = (int) $request->get('page', 1);

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
}