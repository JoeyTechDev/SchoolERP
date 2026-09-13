<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use DateTimeImmutable;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicResultRepository;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\AttendanceRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Services\AuthenticationService;
use SchoolERP\Services\StudentAuthorizationService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

final class StudentPortalController extends Controller
{
    /**
     * Student authorization service.
     */
    private StudentAuthorizationService $authorization;

    /**
     * Authentication service.
     */
    private AuthenticationService $authentication;

    /**
     * Academic result repository.
     */
    private AcademicResultRepository $results;

    /**
     * Attendance repository.
     */
    private AttendanceRepository $attendance;

    /**
     * Academic session repository.
     */
    private AcademicSessionRepository $sessions;

    /**
     * Term repository.
     */
    private TermRepository $terms;

    /**
     * Subject repository.
     */
    private SubjectRepository $subjects;

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
        StudentAuthorizationService $authorization,
        AuthenticationService $authentication,
        AcademicResultRepository $results,
        AttendanceRepository $attendance,
        AcademicSessionRepository $sessions,
        TermRepository $terms,
        SubjectRepository $subjects,
        StudentRepository $students
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->authorization = $authorization;
        $this->authentication = $authentication;
        $this->results = $results;
        $this->attendance = $attendance;
        $this->sessions = $sessions;
        $this->terms = $terms;
        $this->subjects = $subjects;
        $this->students = $students;
    }

    /**
     * Display the Student Portal dashboard.
     */
    public function dashboard(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([3]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student =
            $this->authorization->currentStudent();

        if ($student === null) {
            return Response::make(
                '403 Forbidden - No student profile is linked to this account.',
                403
            );
        }

        $classroom = $student
            ->classroom()
            ->get();

        return $this->view(
            'student.dashboard',
            [
                'title' =>
                    'Student Dashboard',

                'student' =>
                    $student,

                'classroom' =>
                    $classroom,
            ]
        );
    }

    /**
     * Display the current student's profile.
     */
    public function profile(): Response
    {
        $forbidden = $this->requireRole([3]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student =
            $this->authorization->currentStudent();

        if ($student === null) {
            return Response::make(
                '403 Forbidden - No student profile is linked to this account.',
                403
            );
        }

        $classroom = $student
            ->classroom()
            ->get();

        return $this->view(
            'student.profile',
            [
                'title' =>
                    'My Profile',

                'student' =>
                    $student,

                'classroom' =>
                    $classroom,

                'email' =>
                    (string) $this->session->get(
                        'email',
                        ''
                    ),

                'errors' =>
                    $this->session->get(
                        '_errors',
                        []
                    ),

                'oldInput' =>
                    $this->session->get(
                        '_old_input',
                        []
                    ),
            ]
        );
    }

    /**
     * Update the current student's editable profile fields.
     */
    public function updateProfile(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([3]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student =
            $this->authorization->currentStudent();

        if ($student === null) {
            return Response::make(
                '403 Forbidden - No student profile is linked to this account.',
                403
            );
        }

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

        $data = [
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
        ];

        $validator = Validator::make(
            $data,
            [
                'first_name' =>
                    'required|min:2|max:100',

                'last_name' =>
                    'required|min:2|max:100',
            ]
        );

        $errors = [];

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
            $errors['gender'] =
                'Please select a valid gender.';
        }

        if ($dateOfBirth !== '') {
            $date =
                DateTimeImmutable::createFromFormat(
                    'Y-m-d',
                    $dateOfBirth
                );

            if (
                $date === false
                || $date->format('Y-m-d')
                    !== $dateOfBirth
            ) {
                $errors['date_of_birth'] =
                    'Please enter a valid date of birth.';
            }
        }

        if (
            $validator->fails()
            || $errors !== []
        ) {
            $this->session->flash(
                '_old_input',
                $data
            );

            $this->session->flash(
                '_errors',
                array_merge(
                    $validator->errors(),
                    $errors
                )
            );

            return $this->redirect(
                '/SchoolERP/public/student/profile'
            );
        }

        $student->update(
            $data
        );

        $this->session->flash(
            'success',
            'Profile updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/student/profile'
        );
    }

    /**
     * Change the current student's password.
     */
    public function changePassword(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([3]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student =
            $this->authorization->currentStudent();

        if ($student === null) {
            return Response::make(
                '403 Forbidden - No student profile is linked to this account.',
                403
            );
        }

        $currentPassword =
            (string) $request->input(
                'current_password',
                ''
            );

        $newPassword =
            (string) $request->input(
                'new_password',
                ''
            );

        $confirmPassword =
            (string) $request->input(
                'new_password_confirmation',
                ''
            );

        $errors = [];

        if ($currentPassword === '') {
            $errors['current_password'] =
                'Please enter your current password.';
        }

        if ($newPassword === '') {
            $errors['new_password'] =
                'Please enter a new password.';
        } elseif (strlen($newPassword) < 8) {
            $errors['new_password'] =
                'The new password must be at least 8 characters long.';
        } elseif (strlen($newPassword) > 72) {
            $errors['new_password'] =
                'The new password must not exceed 72 characters.';
        }

        if ($confirmPassword === '') {
            $errors['new_password_confirmation'] =
                'Please confirm your new password.';
        } elseif (
            !hash_equals(
                $newPassword,
                $confirmPassword
            )
        ) {
            $errors['new_password_confirmation'] =
                'The password confirmation does not match.';
        }

        if (
            $currentPassword !== ''
            && $newPassword !== ''
            && hash_equals(
                $currentPassword,
                $newPassword
            )
        ) {
            $errors['new_password'] =
                'Your new password must be different from your current password.';
        }

        if ($errors !== []) {
            $this->session->flash(
                '_errors',
                $errors
            );

            return $this->redirect(
                '/SchoolERP/public/student/profile'
                . '#password-security'
            );
        }

        if (
            !$this->authentication->changePassword(
                $currentPassword,
                $newPassword
            )
        ) {
            $this->session->flash(
                '_errors',
                [
                    'current_password' =>
                        'The current password is incorrect.',
                ]
            );

            return $this->redirect(
                '/SchoolERP/public/student/profile'
                . '#password-security'
            );
        }

        $this->session->flash(
            'success',
            'Your password has been changed successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/student/profile'
            . '#password-security'
        );
    }

    /**
     * Display the current student's academic results.
     *
     * A student ID is never accepted from the request.
     */
    public function results(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([3]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student =
            $this->authorization->currentStudent();

        if ($student === null) {
            return Response::make(
                '403 Forbidden - No student profile is linked to this account.',
                403
            );
        }

        $studentId = (int) (
            $student->id ?? 0
        );

        if ($studentId <= 0) {
            return Response::make(
                '403 Forbidden - Invalid student profile.',
                403
            );
        }

        $sessions =
            $this->sessions->allOrdered();

        $terms =
            $this->terms->allOrdered();

        $subjects =
            $this->subjects->allOrdered();

        $sessionId = max(
            0,
            (int) $request->get(
                'academic_session_id',
                0
            )
        );

        $termId = max(
            0,
            (int) $request->get(
                'term_id',
                0
            )
        );

        /*
         * Default to the current academic session.
         */
        if ($sessionId === 0) {
            $currentSession =
                $this->sessions->current();

            if ($currentSession !== null) {
                $sessionId =
                    (int) $currentSession->id;
            }
        }

        /*
         * Default to the first available term when
         * no term was supplied.
         */
        if ($termId === 0) {
            foreach (
                $terms as $term
            ) {
                $candidate =
                    (int) (
                        $term['id'] ?? 0
                    );

                if ($candidate > 0) {
                    $termId = $candidate;
                    break;
                }
            }
        }

        $results = [];

        if (
            $sessionId > 0
            && $termId > 0
        ) {
            $results =
                $this->results->forStudent(
                    $studentId,
                    $sessionId,
                    $termId
                );
        }

        return $this->view(
            'student.results',
            [
                'title' =>
                    'My Results',

                'student' =>
                    $student,

                'sessions' =>
                    $sessions,

                'terms' =>
                    $terms,

                'subjects' =>
                    $subjects,

                'results' =>
                    $results,

                'sessionId' =>
                    $sessionId,

                'termId' =>
                    $termId,
            ]
        );
    }

    /**
     * Display the current student's attendance history.
     *
     * A student ID is never accepted from the request.
     */
    public function attendance(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([3]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $student =
            $this->authorization->currentStudent();

        if ($student === null) {
            return Response::make(
                '403 Forbidden - No student profile is linked to this account.',
                403
            );
        }

        $studentId = (int) (
            $student->id ?? 0
        );

        if ($studentId <= 0) {
            return Response::make(
                '403 Forbidden - Invalid student profile.',
                403
            );
        }

        $sessions =
            $this->sessions->allOrdered();

        $terms =
            $this->terms->allOrdered();

        $sessionId = max(
            0,
            (int) $request->get(
                'academic_session_id',
                0
            )
        );

        $termId = max(
            0,
            (int) $request->get(
                'term_id',
                0
            )
        );

        /*
         * Default to the current academic session.
         */
        if ($sessionId === 0) {
            $currentSession =
                $this->sessions->current();

            if ($currentSession !== null) {
                $sessionId =
                    (int) $currentSession->id;
            }
        }

        /*
         * Default to the first available term.
         */
        if ($termId === 0) {
            foreach (
                $terms as $term
            ) {
                $candidate =
                    (int) (
                        $term['id'] ?? 0
                    );

                if ($candidate > 0) {
                    $termId = $candidate;
                    break;
                }
            }
        }

        $history = [];

        $summary = [
            'total_days' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'attendance_rate' => 0.0,
        ];

        if (
            $sessionId > 0
            && $termId > 0
        ) {
            $history =
                $this->attendance->forStudent(
                    $studentId,
                    $sessionId,
                    $termId
                );

            $summary =
                $this->attendance
                    ->summaryForStudent(
                        $studentId,
                        $sessionId,
                        $termId
                    );
        }

        return $this->view(
            'student.attendance',
            [
                'title' =>
                    'My Attendance',

                'student' =>
                    $student,

                'sessions' =>
                    $sessions,

                'terms' =>
                    $terms,

                'history' =>
                    $history,

                'summary' =>
                    $summary,

                'sessionId' =>
                    $sessionId,

                'termId' =>
                    $termId,
            ]
        );
    }
}
