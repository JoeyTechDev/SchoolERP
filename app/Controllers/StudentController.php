<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\StudentRepository;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Student Controller
 * --------------------------------------------------------------------------
 *
 * Handles student-related HTTP requests.
 *
 * Dependencies are injected automatically through
 * the framework service container.
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
        StudentRepository $students
    ) {
        parent::__construct();

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
                'students' => $pagination,
            ]
        );
    }
}