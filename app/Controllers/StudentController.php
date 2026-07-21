<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Repositories\StudentRepository;

final class StudentController extends Controller
{
    public function __construct(
        private StudentRepository $students
    ) {
    }

    /**
     * Display all students.
     */
    public function index(
        Request $request
    ) {
        $students = $this->students->paginate();

        return $this->json([
            'current_page' => $students->currentPage(),
            'total' => $students->total(),
            'students' => $students->items(),
        ]);
    }
}