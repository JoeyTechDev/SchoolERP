<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Models\Student;
use SchoolERP\Models\User;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

final class StudentAccountController extends Controller
{
    /**
     * Student repository.
     */
    private StudentRepository $students;

    /**
     * User model.
     */
    private User $users;

    /**
     * Student role ID.
     */
    private const ROLE_STUDENT = 3;

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
        $this->users = new User();
    }

    /**
     * Display student account management page.
     */
    public function show(
        int $studentId
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->findStudent(
            $studentId
        );

        if ($student === null) {
            return Response::notFound();
        }

        $user = null;

        if (
            isset($student->user_id)
            && (int) $student->user_id > 0
        ) {
            $user = $this->findUser(
                (int) $student->user_id
            );
        }

        /*
         * Load classroom relationship for the view.
         */
        $student->setRelation(
            'classroom',
            $student->classroom()->get()
        );

        return $this->view(
            'students.account',
            [
                'student' => $student,
                'user' => $user,
            ]
        );
    }

    /**
     * Create the student's login account.
     */
    public function store(
        Request $request,
        int $studentId
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->findStudent(
            $studentId
        );

        if ($student === null) {
            return Response::notFound();
        }

        /*
         * Prevent creation of a second account.
         */
        if (
            isset($student->user_id)
            && (int) $student->user_id > 0
        ) {
            $this->session->flash(
                'error',
                'This student already has a login account.'
            );

            return $this->backToAccount(
                $studentId
            );
        }

        $email = strtolower(
            trim(
                (string) $request->input(
                    'email',
                    ''
                )
            )
        );

        $password = (string) $request->input(
            'password',
            ''
        );

        $passwordConfirmation =
            (string) $request->input(
                'password_confirmation',
                ''
            );

        $data = [
            'email' => $email,
            'password' => $password,
            'password_confirmation' =>
                $passwordConfirmation,
        ];

        $validator = Validator::make(
            $data,
            [
                'email' => 'required|email|max:150',
                'password' => 'required|min:8',
                'password_confirmation' =>
                    'required',
            ]
        );

        $errors = $validator->errors();

        /*
         * Password confirmation.
         */
        if (
            $passwordConfirmation === ''
        ) {
            $errors['password_confirmation'] =
                'Please confirm the password.';
        } elseif (
            !hash_equals(
                $password,
                $passwordConfirmation
            )
        ) {
            $errors['password_confirmation'] =
                'The password confirmation does not match.';
        }

        /*
         * Password maximum length.
         *
         * 72 characters is a safe upper bound
         * for bcrypt-compatible authentication.
         */
        if (
            $password !== ''
            && strlen($password) > 72
        ) {
            $errors['password'] =
                'The password must not exceed 72 characters.';
        }

        /*
         * Email uniqueness.
         */
        if ($email !== '') {
            $existingUser =
                $this->findUserByEmail($email);

            if ($existingUser !== null) {
                $errors['email'] =
                    'This email address is already in use.';
            }
        }

        if ($errors !== []) {
            $this->session->flash(
                '_old_input',
                [
                    'email' => $email,
                ]
            );

            $this->session->flash(
                '_errors',
                $errors
            );

            return $this->backToAccount(
                $studentId
            );
        }

        $userId = 0;

        try {
            /*
             * Create the login user.
             */
            $userId = $this->users->create([
                'role_id' => self::ROLE_STUDENT,
                'first_name' =>
                    trim(
                        (string) (
                            $student->first_name
                            ?? ''
                        )
                    ),
                'last_name' =>
                    trim(
                        (string) (
                            $student->last_name
                            ?? ''
                        )
                    ),
                'email' => $email,
                'password' =>
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    ),
                'status' => 'active',
            ]);

            /*
             * Link the login account to the
             * academic student record.
             */
            $updated = $student->update([
                'user_id' => $userId,
            ]);

            if (!$updated) {
                throw new \RuntimeException(
                    'Unable to link the login account to the student.'
                );
            }

        } catch (\Throwable $exception) {
            /*
             * Best-effort cleanup.
             *
             * Do not leave an orphaned user account
             * if linking the student fails.
             */
            if ($userId > 0) {
                $createdUser =
                    $this->findUser($userId);

                if ($createdUser !== null) {
                    $createdUser->delete();
                }
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Student login account created successfully.'
        );

        return $this->backToAccount(
            $studentId
        );
    }

    /**
     * Reset a student's password.
     */
    public function resetPassword(
        Request $request,
        int $studentId
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student = $this->findStudent(
            $studentId
        );

        if ($student === null) {
            return Response::notFound();
        }

        $user = $this->studentUser(
            $student
        );

        if ($user === null) {
            $this->session->flash(
                'error',
                'This student does not have a login account.'
            );

            return $this->backToAccount(
                $studentId
            );
        }

        /*
         * Make sure the linked account is still
         * a Student account.
         */
        if (
            (int) $user->role_id
            !== self::ROLE_STUDENT
        ) {
            return Response::make(
                '403 Forbidden - The linked account is not a student account.',
                403
            );
        }

        $password = (string) $request->input(
            'password',
            ''
        );

        $confirmation =
            (string) $request->input(
                'password_confirmation',
                ''
            );

        $errors = [];

        if ($password === '') {
            $errors['password'] =
                'Please enter a new password.';
        } elseif (strlen($password) < 8) {
            $errors['password'] =
                'The password must be at least 8 characters long.';
        } elseif (strlen($password) > 72) {
            $errors['password'] =
                'The password must not exceed 72 characters.';
        }

        if ($confirmation === '') {
            $errors['password_confirmation'] =
                'Please confirm the new password.';
        } elseif (
            !hash_equals(
                $password,
                $confirmation
            )
        ) {
            $errors['password_confirmation'] =
                'The password confirmation does not match.';
        }

        if ($errors !== []) {
            $this->session->flash(
                '_errors',
                $errors
            );

            return $this->backToAccount(
                $studentId
            );
        }

        $updated = $user->update([
            'password' =>
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                ),
        ]);

        if ($updated === false) {
            $this->session->flash(
                'error',
                'Unable to reset the student password.'
            );

            return $this->backToAccount(
                $studentId
            );
        }

        $this->session->flash(
            'success',
            'Student password reset successfully.'
        );

        return $this->backToAccount(
            $studentId
        );
    }

    /**
     * Suspend a student's login account.
     */
    public function suspend(
        int $studentId
    ): Response {
        return $this->changeStatus(
            $studentId,
            'suspended',
            'Student login account suspended successfully.'
        );
    }

    /**
     * Activate a student's login account.
     */
    public function activate(
        int $studentId
    ): Response {
        return $this->changeStatus(
            $studentId,
            'active',
            'Student login account activated successfully.'
        );
    }

    /**
     * Change the account status.
     */
    private function changeStatus(
        int $studentId,
        string $status,
        string $successMessage
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        if (
            !in_array(
                $status,
                [
                    'active',
                    'suspended',
                    'inactive',
                ],
                true
            )
        ) {
            return Response::make(
                'Invalid account status.',
                400
            );
        }

        $student = $this->findStudent(
            $studentId
        );

        if ($student === null) {
            return Response::notFound();
        }

        $user = $this->studentUser(
            $student
        );

        if ($user === null) {
            $this->session->flash(
                'error',
                'This student does not have a login account.'
            );

            return $this->backToAccount(
                $studentId
            );
        }

        /*
         * Student account management must never
         * modify another role's account.
         */
        if (
            (int) $user->role_id
            !== self::ROLE_STUDENT
        ) {
            return Response::make(
                '403 Forbidden - The linked account is not a student account.',
                403
            );
        }

        $updated = $user->update([
            'status' => $status,
        ]);

        if ($updated === false) {
            $this->session->flash(
                'error',
                'Unable to update the student account status.'
            );

            return $this->backToAccount(
                $studentId
            );
        }

        $this->session->flash(
            'success',
            $successMessage
        );

        return $this->backToAccount(
            $studentId
        );
    }

    /**
     * Find a student.
     */
    private function findStudent(
        int $studentId
    ): ?Student {
        if ($studentId <= 0) {
            return null;
        }

        return $this->students->find(
            $studentId
        );
    }

    /**
     * Find a User by ID.
     */
    private function findUser(
        int $userId
    ): ?User {
        if ($userId <= 0) {
            return null;
        }

        return $this->users->find(
            $userId
        );
    }

    /**
     * Find a User by email address.
     */
    private function findUserByEmail(
        string $email
    ): ?User {
        if ($email === '') {
            return null;
        }

        $record = $this->users
            ->query()
            ->where(
                'email',
                '=',
                $email
            )
            ->first();

        if ($record === null) {
            return null;
        }

        return (new User())->fill(
            $record
        );
    }

    /**
     * Resolve the account linked to a student.
     */
    private function studentUser(
        Student $student
    ): ?User {
        $userId = (int) (
            $student->user_id ?? 0
        );

        if ($userId <= 0) {
            return null;
        }

        return $this->findUser(
            $userId
        );
    }

    /**
     * Return to the student account page.
     */
    private function backToAccount(
        int $studentId
    ): Response {
        return $this->redirect(
            '/SchoolERP/public/students/'
            . $studentId
            . '/account'
        );
    }
}
