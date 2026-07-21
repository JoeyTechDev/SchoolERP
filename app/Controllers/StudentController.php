<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Repositories\StudentRepository;

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
        $this->students = $students;
    }

    /**
     * Display all students.
     */
    public function index(
        Request $request
    ) {
        $page = (int) $request->input(
            'page',
            1
        );

        $students = $this->students->paginate(
            $page,
            10
        );

        return $this->view(
            'students.index',
            [
                'students' => $students,
            ]
        );
    }
}