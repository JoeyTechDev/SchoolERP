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
    public function __construct()
    {
        parent::__construct();

        $this->students = new StudentRepository();
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
                'students' => $pagination,
            ]
        );
    }
}