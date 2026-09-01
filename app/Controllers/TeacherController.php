<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use DateTime;
use PDOException;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\TeacherRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Teacher Controller
 * --------------------------------------------------------------------------
 *
 * Handles teacher management.
 */
final class TeacherController extends Controller
{
    /**
     * Teacher repository.
     */
    private TeacherRepository $teachers;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        TeacherRepository $teachers
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->teachers = $teachers;
    }

    /**
     * Display teachers.
     */
    public function index(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

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
            $pagination = $this->teachers->searchPaginated(
                $search,
                $page,
                10
            );
        } else {
            $pagination = $this->teachers->paginate(
                $page,
                10
            );
        }

        return $this->view(
            'teachers.index',
            [
                'title' => 'Teachers',
                'teachers' => $pagination->items(),
                'pagination' => $pagination,
                'search' => $search,
            ]
        );
    }

    /**
     * Display one teacher.
     */
    public function show(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $teacher = $this->teachers->find(
            $id
        );

        if ($teacher === null) {
            return Response::notFound();
        }

        return $this->view(
            'teachers.show',
            [
                'title' => 'Teacher Details',
                'teacher' => $teacher,
            ]
        );
    }

    /**
     * Show create form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        return $this->view(
            'teachers.create',
            [
                'title' => 'Create Teacher',
            ]
        );
    }

    /**
     * Store a new teacher.
     */
    public function store(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $data = $this->teacherInput(
            $request
        );

        $manualErrors = $this->validateTeacherDates(
            $data
        );

        $validator = Validator::make(
            $data,
            [
                'employee_number' =>
                    'required|min:2|max:50',

                'first_name' =>
                    'required|min:2|max:100',

                'last_name' =>
                    'required|min:2|max:100',

                'gender' =>
                    'nullable|max:20',

                'phone' =>
                    'nullable|max:30',

                'email' =>
                    'nullable|max:150',

                'address' =>
                    'nullable|max:500',

                'employment_status' =>
                    'required|max:30',
            ]
        );

        $manualErrors = array_merge(
            $manualErrors,
            $this->validateTeacherValues(
                $data
            )
        );

        if (
            $validator->fails()
            || $manualErrors !== []
        ) {
            $this->flashValidationFailure(
                $data,
                array_merge(
                    $validator->errors(),
                    $manualErrors
                )
            );

            return $this->redirect(
                '/SchoolERP/public/teachers/create'
            );
        }

        try {
            $this->teachers->create(
                $data
            );
        } catch (PDOException $exception) {

            if (
                $this->isDuplicateEmployeeNumberException(
                    $exception
                )
            ) {
                $this->flashValidationFailure(
                    $data,
                    [
                        'employee_number' =>
                            'This employee number is already assigned to another teacher.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/teachers/create'
                );
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Teacher created successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/teachers'
        );
    }

    /**
     * Show edit form.
     */
    public function edit(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $teacher = $this->teachers->find(
            $id
        );

        if ($teacher === null) {
            return Response::notFound();
        }

        return $this->view(
            'teachers.edit',
            [
                'title' => 'Edit Teacher',
                'teacher' => $teacher,
            ]
        );
    }

    /**
     * Update an existing teacher.
     */
    public function update(
        Request $request,
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $teacher = $this->teachers->find(
            $id
        );

        if ($teacher === null) {
            return Response::notFound();
        }

        $data = $this->teacherInput(
            $request
        );

        $validator = Validator::make(
            $data,
            [
                'employee_number' =>
                    'required|min:2|max:50',

                'first_name' =>
                    'required|min:2|max:100',

                'last_name' =>
                    'required|min:2|max:100',

                'gender' =>
                    'nullable|max:20',

                'phone' =>
                    'nullable|max:30',

                'email' =>
                    'nullable|max:150',

                'address' =>
                    'nullable|max:500',

                'employment_status' =>
                    'required|max:30',
            ]
        );

        $manualErrors = array_merge(
            $this->validateTeacherDates(
                $data
            ),
            $this->validateTeacherValues(
                $data
            )
        );

        if (
            $validator->fails()
            || $manualErrors !== []
        ) {
            $this->flashValidationFailure(
                $data,
                array_merge(
                    $validator->errors(),
                    $manualErrors
                )
            );

            return $this->redirect(
                '/SchoolERP/public/teachers/'
                . $id
                . '/edit'
            );
        }

        try {
            $teacher->update(
                $data
            );
        } catch (PDOException $exception) {

            if (
                $this->isDuplicateEmployeeNumberException(
                    $exception
                )
            ) {
                $this->flashValidationFailure(
                    $data,
                    [
                        'employee_number' =>
                            'This employee number is already assigned to another teacher.',
                    ]
                );

                return $this->redirect(
                    '/SchoolERP/public/teachers/'
                    . $id
                    . '/edit'
                );
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Teacher updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/teachers/'
            . $id
        );
    }

    /**
     * Delete a teacher.
     */
    public function destroy(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $teacher = $this->teachers->find(
            $id
        );

        if ($teacher === null) {
            return Response::notFound();
        }

        /*
         * Teacher assignments are protected by ON DELETE CASCADE
         * for teacher_id, so deleting a teacher also removes the
         * teacher's assignment rows.
         */
        try {
            $deleted = $teacher->delete();
        } catch (PDOException $exception) {

            $this->session->flash(
                'error',
                'Unable to delete this teacher.'
            );

            return $this->redirect(
                '/SchoolERP/public/teachers/'
                . $id
            );
        }

        if (!$deleted) {
            $this->session->flash(
                'error',
                'Unable to delete this teacher.'
            );

            return $this->redirect(
                '/SchoolERP/public/teachers/'
                . $id
            );
        }

        $this->session->flash(
            'success',
            'Teacher deleted successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/teachers'
        );
    }

    /**
     * Read and normalize teacher form input.
     *
     * @return array<string,mixed>
     */
    private function teacherInput(
        Request $request
    ): array {
        $employeeNumber = strtoupper(
            trim(
                (string) $request->input(
                    'employee_number',
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

        $phone = trim(
            (string) $request->input(
                'phone',
                ''
            )
        );

        $email = strtolower(
            trim(
                (string) $request->input(
                    'email',
                    ''
                )
            )
        );

        $address = trim(
            (string) $request->input(
                'address',
                ''
            )
        );

        $employmentStatus = strtolower(
            trim(
                (string) $request->input(
                    'employment_status',
                    'active'
                )
            )
        );

        $dateEmployed = trim(
            (string) $request->input(
                'date_employed',
                ''
            )
        );

        return [
            'employee_number' =>
                $employeeNumber,

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

            'phone' =>
                $phone !== ''
                    ? $phone
                    : null,

            'email' =>
                $email !== ''
                    ? $email
                    : null,

            'address' =>
                $address !== ''
                    ? $address
                    : null,

            'employment_status' =>
                $employmentStatus,

            'date_employed' =>
                $dateEmployed !== ''
                    ? $dateEmployed
                    : null,
        ];
    }

    /**
     * Validate teacher values not covered by the basic Validator.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,string>
     */
    private function validateTeacherValues(
        array $data
    ): array {
        $errors = [];

        $allowedGenders = [
            'male',
            'female',
            'other',
        ];

        $gender = $data['gender'] ?? null;

        if (
            $gender !== null
            && !in_array(
                $gender,
                $allowedGenders,
                true
            )
        ) {
            $errors['gender'] =
                'Please select a valid gender.';
        }

        $allowedStatuses = [
            'active',
            'inactive',
            'suspended',
            'terminated',
        ];

        $status = (string) (
            $data['employment_status']
            ?? ''
        );

        if (
            !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $errors['employment_status'] =
                'Please select a valid employment status.';
        }

        $email = $data['email'] ?? null;

        if (
            $email !== null
            && $email !== ''
            && filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            $errors['email'] =
                'Please enter a valid email address.';
        }

        return $errors;
    }

    /**
     * Validate teacher date fields.
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,string>
     */
    private function validateTeacherDates(
        array $data
    ): array {
        $errors = [];

        foreach (
            [
                'date_of_birth' => 'Date of birth',
                'date_employed' => 'Date employed',
            ]
            as $field => $label
        ) {
            $value = $data[$field] ?? null;

            if (
                $value === null
                || $value === ''
            ) {
                continue;
            }

            $date = DateTime::createFromFormat(
                'Y-m-d',
                (string) $value
            );

            if (
                $date === false
                || $date->format('Y-m-d')
                    !== $value
            ) {
                $errors[$field] =
                    $label . ' must be a valid date.';
            }
        }

        return $errors;
    }

    /**
     * Store form input and errors in the session.
     *
     * @param array<string,mixed> $data
     * @param array<string,string> $errors
     */
    private function flashValidationFailure(
        array $data,
        array $errors
    ): void {
        $this->session->flash(
            '_old_input',
            $data
        );

        $this->session->flash(
            '_errors',
            $errors
        );
    }

    /**
     * Detect a duplicate employee-number database error.
     */
    private function isDuplicateEmployeeNumberException(
        PDOException $exception
    ): bool {
        $message = strtolower(
            $exception->getMessage()
        );

        return $exception->getCode() === '23000'
            && (
                str_contains(
                    $message,
                    'employee_number'
                )
                || str_contains(
                    $message,
                    'teachers_employee_number_unique'
                )
            );
    }
}
