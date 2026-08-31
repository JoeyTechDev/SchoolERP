<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use PDOException;
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
            (int) $request->get(
                'page',
                1
            )
        );

        $search = trim(
            (string) $request->get(
                'q',
                ''
            )
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

        $student = $this->students->find(
            $id
        );

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
        $forbidden = $this->requireRole([1]);

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
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        /*
         * --------------------------------------------------------------
         * Read input
         * --------------------------------------------------------------
         */

        $admissionNumber = strtoupper(
            trim(
                (string) $request->input(
                    'admission_number',
                    ''
                )
            )
        );

        $firstName = trim(
            (string) $request->input(
                'first_name',
                ''
            )
        );

        $lastName = trim(
            (string) $request->input(
                'last_name',
                ''
            )
        );

        $dateOfBirth = trim(
            (string) $request->input(
                'date_of_birth',
                ''
            )
        );

        $gender = strtolower(
            trim(
                (string) $request->input(
                    'gender',
                    ''
                )
            )
        );

        $classroomId = $request->input(
            'classroom_id',
            ''
        );

        $classroomId =
            trim((string) $classroomId) === ''
                ? null
                : (int) $classroomId;

        /*
         * --------------------------------------------------------------
         * Prepare data
         * --------------------------------------------------------------
         */

        $data = [
            'admission_number' =>
                $admissionNumber !== ''
                    ? $admissionNumber
                    : null,

            'first_name' =>
                $firstName,

            'last_name' =>
                $lastName,

            'date_of_birth' =>
                $dateOfBirth !== ''
                    ? $dateOfBirth
                    : null,

            'gender' =>
                $gender !== ''
                    ? $gender
                    : null,

            'classroom_id' =>
                $classroomId,
        ];

        /*
         * --------------------------------------------------------------
         * Validation
         * --------------------------------------------------------------
         */

        $validator = Validator::make(
            $data,
            [
                'admission_number' =>
                    'required|min:2|max:50',

                'first_name' =>
                    'required|min:2|max:100',

                'last_name' =>
                    'required|min:2|max:100',

                'classroom_id' =>
                    'nullable|integer|min:1',
            ]
        );

        $manualErrors = [];

        /*
         * Validate gender.
         */
        if (
            $gender !== ''
            && !in_array(
                $gender,
                [
                    'male',
                    'female',
                    'other',
                ],
                true
            )
        ) {
            $manualErrors['gender'] =
                'Please select a valid gender.';
        }

        /*
         * Validate date of birth.
         */
        if ($dateOfBirth !== '') {
            $date = \DateTime::createFromFormat(
                'Y-m-d',
                $dateOfBirth
            );

            if (
                $date === false
                || $date->format('Y-m-d')
                    !== $dateOfBirth
            ) {
                $manualErrors['date_of_birth'] =
                    'Please enter a valid date of birth.';
            }
        }

        /*
         * Validate classroom.
         */
        if ($classroomId !== null) {
            $classroom = $this->classrooms->find(
                $classroomId
            );

            if ($classroom === null) {
                $manualErrors['classroom_id'] =
                    'Selected classroom does not exist.';
            }
        }

        /*
         * Return validation errors.
         */
        if (
            $validator->fails()
            || $manualErrors !== []
        ) {
            $this->session->flash(
                '_old_input',
                $data
            );

            $this->session->flash(
                '_errors',
                array_merge(
                    $validator->errors(),
                    $manualErrors
                )
            );

            return $this->redirect(
                '/SchoolERP/public/students/create'
            );
        }

        /*
         * --------------------------------------------------------------
         * Save
         * --------------------------------------------------------------
         */

        try {
            $this->students->create(
                $data
            );
        } catch (PDOException $exception) {

            if (
                $this->isDuplicateAdmissionException(
                    $exception
                )
            ) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'admission_number' =>
                            'This admission number is already assigned to another student.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/students/create'
                );
            }

            throw $exception;
        }

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
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->students->find(
            $id
        );

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
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->students->find(
            $id
        );

        if ($student === null) {
            return Response::notFound();
        }

        /*
         * --------------------------------------------------------------
         * Read input
         * --------------------------------------------------------------
         */

        $admissionNumber = strtoupper(
            trim(
                (string) $request->input(
                    'admission_number',
                    ''
                )
            )
        );

        $firstName = trim(
            (string) $request->input(
                'first_name',
                ''
            )
        );

        $lastName = trim(
            (string) $request->input(
                'last_name',
                ''
            )
        );

        $dateOfBirth = trim(
            (string) $request->input(
                'date_of_birth',
                ''
            )
        );

        $gender = strtolower(
            trim(
                (string) $request->input(
                    'gender',
                    ''
                )
            )
        );

        $classroomId = $request->input(
            'classroom_id',
            ''
        );

        $classroomId =
            trim((string) $classroomId) === ''
                ? null
                : (int) $classroomId;

        /*
         * --------------------------------------------------------------
         * Prepare data
         * --------------------------------------------------------------
         */

        $data = [
            'admission_number' =>
                $admissionNumber !== ''
                    ? $admissionNumber
                    : null,

            'first_name' =>
                $firstName,

            'last_name' =>
                $lastName,

            'date_of_birth' =>
                $dateOfBirth !== ''
                    ? $dateOfBirth
                    : null,

            'gender' =>
                $gender !== ''
                    ? $gender
                    : null,

            'classroom_id' =>
                $classroomId,
        ];

        /*
         * --------------------------------------------------------------
         * Validation
         * --------------------------------------------------------------
         */

        $validator = Validator::make(
            $data,
            [
                'admission_number' =>
                    'required|min:2|max:50',

                'first_name' =>
                    'required|min:2|max:100',

                'last_name' =>
                    'required|min:2|max:100',

                'classroom_id' =>
                    'nullable|integer|min:1',
            ]
        );

        $manualErrors = [];

        /*
         * Validate gender.
         */
        if (
            $gender !== ''
            && !in_array(
                $gender,
                [
                    'male',
                    'female',
                    'other',
                ],
                true
            )
        ) {
            $manualErrors['gender'] =
                'Please select a valid gender.';
        }

        /*
         * Validate date of birth.
         */
        if ($dateOfBirth !== '') {
            $date = \DateTime::createFromFormat(
                'Y-m-d',
                $dateOfBirth
            );

            if (
                $date === false
                || $date->format('Y-m-d')
                    !== $dateOfBirth
            ) {
                $manualErrors['date_of_birth'] =
                    'Please enter a valid date of birth.';
            }
        }

        /*
         * Validate classroom.
         */
        if ($classroomId !== null) {
            $classroom = $this->classrooms->find(
                $classroomId
            );

            if ($classroom === null) {
                $manualErrors['classroom_id'] =
                    'Selected classroom does not exist.';
            }
        }

        /*
         * Return validation errors.
         */
        if (
            $validator->fails()
            || $manualErrors !== []
        ) {
            $this->session->flash(
                '_old_input',
                $data
            );

            $this->session->flash(
                '_errors',
                array_merge(
                    $validator->errors(),
                    $manualErrors
                )
            );

            return $this->redirect(
                '/SchoolERP/public/students/'
                . $id
                . '/edit'
            );
        }

        /*
         * --------------------------------------------------------------
         * Update database
         * --------------------------------------------------------------
         */

        try {
            $student->update(
                $data
            );
        } catch (PDOException $exception) {

            if (
                $this->isDuplicateAdmissionException(
                    $exception
                )
            ) {
                $this->session->flash(
                    '_old_input',
                    $data
                );

                $this->session->flash(
                    '_errors',
                    [
                        'admission_number' =>
                            'This admission number is already assigned to another student.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/students/'
                    . $id
                    . '/edit'
                );
            }

            throw $exception;
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

        $student = $this->students->find(
            $id
        );

        if ($student === null) {
            return Response::notFound();
        }

        if (
            !$this->students->delete(
                $id
            )
        ) {
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

    /**
     * Determine whether an exception represents
     * a duplicate admission-number violation.
     */
    private function isDuplicateAdmissionException(
        PDOException $exception
    ): bool {
        $message = strtolower(
            $exception->getMessage()
        );

        return $exception->getCode() === '23000'
            && (
                str_contains(
                    $message,
                    'admission_number'
                )
                || str_contains(
                    $message,
                    'students_admission_number_unique'
                )
            );
    }
}