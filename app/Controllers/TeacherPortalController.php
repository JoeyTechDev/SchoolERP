<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TeacherAssignmentRepository;
use SchoolERP\Services\TeacherAuthorizationService;
use SchoolERP\Services\AuthenticationService;
use DateTimeImmutable;
use SchoolERP\Validation\Validator;
use SchoolERP\Models\Student;
use SchoolERP\Query\Pagination\Paginator;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

final class TeacherPortalController extends Controller
{
    /**
     * Teacher assignment repository.
     */
    private TeacherAssignmentRepository $assignments;

    /**
     * Classroom repository.
     */
    private ClassroomRepository $classrooms;

    /**
     * Subject repository.
     */
    private SubjectRepository $subjects;

    /**
     * Student repository.
     */
    private StudentRepository $students;

    /**
     * Teacher authorization service.
     */
    private TeacherAuthorizationService $authorization;

    /**
     * Authentication service.
     */
    private AuthenticationService $authentication;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        TeacherAssignmentRepository $assignments,
        ClassroomRepository $classrooms,
        SubjectRepository $subjects,
        StudentRepository $students,
        TeacherAuthorizationService $authorization,
        AuthenticationService $authentication

    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->assignments = $assignments;
        $this->classrooms = $classrooms;
        $this->subjects = $subjects;
        $this->students = $students;
        $this->authorization = $authorization;
        $this->authentication = $authentication;
    }

    /**
     * Display the teacher dashboard.
     */
    public function dashboard(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $teacher =
            $this->authorization->currentTeacher();

        if ($teacher === null) {
            return Response::make(
                '403 Forbidden - No teacher profile is linked to this account.',
                403
            );
        }

        $teacherId = (int) (
            $teacher->id ?? 0
        );

        if ($teacherId <= 0) {
            return Response::make(
                '403 Forbidden - Invalid teacher profile.',
                403
            );
        }

        /*
         * Only active teaching assignments are used
         * on the portal dashboard.
         */
        $assignments =
            $this->assignments->forTeacher(
                $teacherId,
                true
            );

        /*
         * Build classroom and subject lookup tables.
         */
        $classroomRecords =
            $this->classrooms->allOrdered();

        $subjectRecords =
            $this->subjects->allOrdered();

        $students =
            $this->students->allOrdered();

        $classroomLookup = [];

        foreach (
            $classroomRecords
            as $classroom
        ) {
            $classroomId = (int) (
                $classroom['id'] ?? 0
            );

            if ($classroomId <= 0) {
                continue;
            }

            $classroomLookup[$classroomId] =
                (string) (
                    $classroom['name'] ?? ''
                );
        }

        $subjectLookup = [];

        foreach (
            $subjectRecords
            as $subject
        ) {
            $subjectId = (int) (
                $subject['id'] ?? 0
            );

            if ($subjectId <= 0) {
                continue;
            }

            $subjectLookup[$subjectId] = [
                'name' => (string) (
                    $subject['name'] ?? ''
                ),
                'code' => (string) (
                    $subject['code'] ?? ''
                ),
            ];
        }

        /*
         * Count students by classroom.
         */
        $studentCounts = [];

        foreach (
            $students
            as $student
        ) {
            $classroomId = (int) (
                $student['classroom_id']
                ?? 0
            );

            if ($classroomId <= 0) {
                continue;
            }

            if (
                !isset(
                    $studentCounts[$classroomId]
                )
            ) {
                $studentCounts[$classroomId] = 0;
            }

            $studentCounts[$classroomId]++;
        }

        /*
         * Build dashboard classroom cards.
         *
         * One classroom may contain several subject
         * assignments for the same teacher.
         */
        $classroomCards = [];

        foreach (
            $assignments
            as $assignment
        ) {
            $classroomId = (int) (
                $assignment->classroom_id
                ?? 0
            );

            $subjectId = (int) (
                $assignment->subject_id
                ?? 0
            );

            if (
                $classroomId <= 0
                || $subjectId <= 0
            ) {
                continue;
            }

            if (
                !isset(
                    $classroomCards[$classroomId]
                )
            ) {
                $classroomCards[$classroomId] = [
                    'id' => $classroomId,

                    'name' =>
                        $classroomLookup[
                            $classroomId
                        ]
                        ?? 'Classroom #'
                            . $classroomId,

                    'student_count' =>
                        $studentCounts[
                            $classroomId
                        ]
                        ?? 0,

                    'subjects' => [],
                ];
            }

            if (
                isset(
                    $subjectLookup[$subjectId]
                )
            ) {
                $classroomCards[
                    $classroomId
                ]['subjects'][$subjectId] =
                    $subjectLookup[$subjectId];
            }
        }

        /*
         * Re-index classroom cards and subjects.
         */
        $classroomCards =
            array_values(
                $classroomCards
            );

        foreach (
            $classroomCards
            as &$classroomCard
        ) {
            $classroomCard['subjects'] =
                array_values(
                    $classroomCard['subjects']
                );
        }

        unset($classroomCard);

        /*
         * Build a unique subject list across all
         * active assignments.
         */
        $mySubjects = [];

        foreach (
            $assignments
            as $assignment
        ) {
            $subjectId = (int) (
                $assignment->subject_id
                ?? 0
            );

            if (
                $subjectId <= 0
                || !isset(
                    $subjectLookup[$subjectId]
                )
            ) {
                continue;
            }

            $mySubjects[$subjectId] =
                $subjectLookup[$subjectId];
        }

        $mySubjects =
            array_values(
                $mySubjects
            );

        /*
         * Calculate total students across assigned classrooms.
         *
         * array_unique prevents counting a classroom twice when
         * the teacher teaches several subjects in that classroom.
         */
        $assignedClassroomIds = array_map(
            static function (
                array $classroom
            ): int {
                return (int) (
                    $classroom['id'] ?? 0
                );
            },
            $classroomCards
        );

        $totalStudents = 0;

        foreach (
            $assignedClassroomIds
            as $classroomId
        ) {
            $totalStudents +=
                $studentCounts[$classroomId]
                ?? 0;
        }

        $firstName = trim(
            (string) (
                $teacher->first_name ?? ''
            )
        );

        $lastName = trim(
            (string) (
                $teacher->last_name ?? ''
            )
        );

        $teacherName = trim(
            $firstName
            . ' '
            . $lastName
        );

        if ($teacherName === '') {
            $teacherName = 'Teacher';
        }

        return $this->view(
            'teacher.dashboard',
            [
                'title' =>
                    'Teacher Dashboard',

                'teacher' =>
                    $teacher,

                'teacherName' =>
                    $teacherName,

                'assignments' =>
                    $assignments,

                'classroomCards' =>
                    $classroomCards,

                'mySubjects' =>
                    $mySubjects,

                'totalStudents' =>
                    $totalStudents,

                'totalClassrooms' =>
                    count(
                        $classroomCards
                    ),

                'totalSubjects' =>
                    count(
                        $mySubjects
                    ),
            ]
        );
    }

/**
 * Display students belonging to the current teacher's
 * assigned classrooms.
 */
public function students(
    Request $request
): Response {
    $forbidden = $this->requireRole([2]);

    if ($forbidden !== null) {
        return $forbidden;
    }

    $teacher =
        $this->authorization->currentTeacher();

    if ($teacher === null) {
        return Response::make(
            '403 Forbidden - No teacher profile is linked to this account.',
            403
        );
    }

    $teacherId = (int) (
        $teacher->id ?? 0
    );

    if ($teacherId <= 0) {
        return Response::make(
            '403 Forbidden - Invalid teacher profile.',
            403
        );
    }

    /*
     * Get active assignments for this teacher.
     */
    $assignments =
        $this->assignments->forTeacher(
            $teacherId,
            true
        );

    /*
     * Collect unique classroom IDs.
     */
    $assignedClassroomIds = [];

    foreach (
        $assignments
        as $assignment
    ) {
        $classroomId = (int) (
            $assignment->classroom_id
            ?? 0
        );

        if ($classroomId > 0) {
            $assignedClassroomIds[
                $classroomId
            ] = true;
        }
    }

    /*
     * Load all students and filter strictly by
     * assigned classroom.
     */
    $allStudents =
        $this->students->allOrdered();

    $students = [];

    foreach (
        $allStudents
        as $student
    ) {
        $studentClassroomId = (int) (
            $student['classroom_id']
            ?? 0
        );

        if (
            $studentClassroomId <= 0
            || !isset(
                $assignedClassroomIds[
                    $studentClassroomId
                ]
            )
        ) {
            continue;
        }

        $students[] = $student;
    }

    /*
     * Search.
     */
    $search = trim(
        (string) $request->get(
            'q',
            ''
        )
    );

    if ($search !== '') {

        $searchLower =
            strtolower($search);

        $students = array_values(
            array_filter(
                $students,
                static function (
                    array $student
                ) use (
                    $searchLower
                ): bool {

                    $firstName =
                        strtolower(
                            trim(
                                (string) (
                                    $student[
                                        'first_name'
                                    ] ?? ''
                                )
                            )
                        );

                    $lastName =
                        strtolower(
                            trim(
                                (string) (
                                    $student[
                                        'last_name'
                                    ] ?? ''
                                )
                            )
                        );

                    $admissionNumber =
                        strtolower(
                            trim(
                                (string) (
                                    $student[
                                        'admission_number'
                                    ] ?? ''
                                )
                            )
                        );

                    $fullName =
                        trim(
                            $firstName
                            . ' '
                            . $lastName
                        );

                    return str_contains(
                        $firstName,
                        $searchLower
                    )
                    || str_contains(
                        $lastName,
                        $searchLower
                    )
                    || str_contains(
                        $fullName,
                        $searchLower
                    )
                    || str_contains(
                        $admissionNumber,
                        $searchLower
                    );
                }
            )
        );
    }

    /*
     * Classroom lookup.
     */
    $classroomRecords =
        $this->classrooms->allOrdered();

    $classroomLookup = [];

    foreach (
        $classroomRecords
        as $classroom
    ) {
        $classroomId = (int) (
            $classroom['id'] ?? 0
        );

        if ($classroomId <= 0) {
            continue;
        }

        $classroomLookup[
            $classroomId
        ] = (string) (
            $classroom['name'] ?? ''
        );
    }

    /*
     * Attach classroom names.
     */
    foreach (
        $students
        as &$student
    ) {
        $classroomId = (int) (
            $student['classroom_id']
            ?? 0
        );

        $student['classroom_name'] =
            $classroomLookup[
                $classroomId
            ]
            ?? 'Unknown Classroom';
    }

    unset($student);

    /*
     * Simple pagination for the Teacher Portal.
     */
    $page = max(
        1,
        (int) $request->get(
            'page',
            1
        )
    );

    $perPage = 10;

    $total = count($students);

    $offset =
        ($page - 1)
        * $perPage;

    $pageItems = array_slice(
        $students,
        $offset,
        $perPage
    );

    $pagination = new Paginator(
        $pageItems,
        $total,
        $perPage,
        $page
    );

    return $this->view(
        'teacher.students',
        [
            'title' =>
                'My Students',

            'students' =>
                $pagination->items(),

            'pagination' =>
                $pagination,

            'search' =>
                $search,
        ]
    );
}

/**
 * Display one student from the current teacher's
 * assigned classrooms.
 */
public function student(
    int $id
): Response {
    $forbidden = $this->requireRole([2]);

    if ($forbidden !== null) {
        return $forbidden;
    }

    /*
     * The authorization service is the security boundary.
     *
     * A teacher cannot access a student outside an
     * assigned classroom even if the ID is manually
     * entered into the URL.
     */
    if (
        !$this->authorization->canManageStudent(
            $id
        )
    ) {
        return Response::make(
            '403 Forbidden - You are not authorized to access this student.',
            403
        );
    }

    $student =
        $this->students->find(
            $id
        );

    if ($student === null) {
        return Response::notFound();
    }

    /*
     * Load classroom relationship for the existing
     * students.show view.
     */
    $student->setRelation(
        'classroom',
        $student->classroom()->get()
    );

    return $this->view(
        'teacher.student',
        [
            'title' =>
                'Student Details',

            'student' =>
                $student,
        ]
    );
}

/**
 * Display the current teacher's profile.
 */
public function profile(): Response
{
    $forbidden = $this->requireRole([2]);

    if ($forbidden !== null) {
        return $forbidden;
    }

    $teacher =
        $this->authorization->currentTeacher();

    if ($teacher === null) {
        return Response::make(
            '403 Forbidden - No teacher profile is linked to this account.',
            403
        );
    }

    return $this->view(
        'teacher.profile',
        [
            'title' => 'My Profile',
            'teacher' => $teacher,
        ]
    );
}

/**
 * Update the current teacher's profile.
 *
 * Teachers can update personal/contact information only.
 * Employment identity remains administrator-controlled.
 */
public function updateProfile(
    Request $request
): Response {
    $forbidden = $this->requireRole([2]);

    if ($forbidden !== null) {
        return $forbidden;
    }

    $teacher =
        $this->authorization->currentTeacher();

    if ($teacher === null) {
        return Response::make(
            '403 Forbidden - No teacher profile is linked to this account.',
            403
        );
    }

    /*
     * Read input.
     */
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

    $email = trim(
        (string) $request->input(
            'email',
            ''
        )
    );

    $address = trim(
        (string) $request->input(
            'address',
            ''
        )
    );

    /*
     * Prepare data.
     */
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
    ];

    /*
     * Validate basic fields.
     */
    $validator = Validator::make(
        $data,
        [
            'first_name' =>
                'required|min:2|max:100',

            'last_name' =>
                'required|min:2|max:100',

            'email' =>
                'nullable|email|max:150',
        ]
    );

    $manualErrors = [];

    /*
     * Gender.
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
     * Date of birth.
     */
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
            $manualErrors['date_of_birth'] =
                'Please enter a valid date of birth.';
        }
    }

    /*
     * Email uniqueness is intentionally not checked
     * here because the Teacher profile email is not
     * necessarily the login account email.
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
            '/SchoolERP/public/teacher/profile'
        );
    }

    /*
     * Update only the fields teachers are allowed
     * to change.
     */
    $updated = $teacher->update(
        $data
    );

    /*
     * Some ORM implementations return 0 when the
     * submitted values are identical to existing values.
     * The request was still processed successfully.
     */
    if (
        $updated === false
    ) {
        $this->session->flash(
            'success',
            'Profile information saved.'
        );
    } else {
        $this->session->flash(
            'success',
            'Profile updated successfully.'
        );
    }

    return $this->redirect(
        '/SchoolERP/public/teacher/profile'
    );
}

/**
 * Change the current teacher's password.
 */
public function changePassword(
    Request $request
): Response {
    $forbidden = $this->requireRole([2]);

    if ($forbidden !== null) {
        return $forbidden;
    }

    /*
     * Ensure the account is linked to a teacher profile.
     */
    $teacher =
        $this->authorization->currentTeacher();

    if ($teacher === null) {
        return Response::make(
            '403 Forbidden - No teacher profile is linked to this account.',
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

    /*
     * Current password is required.
     */
    if ($currentPassword === '') {
        $errors['current_password'] =
            'Please enter your current password.';
    }

    /*
     * Password length.
     */
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

    /*
     * Confirmation.
     */
    if (
        $confirmPassword === ''
    ) {
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

    /*
     * Prevent reusing the same password.
     */
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
            '/SchoolERP/public/teacher/profile'
            . '#password-security'
        );
    }

    /*
     * AuthenticationService verifies the current password,
     * hashes the new password, updates the account, and
     * regenerates the session ID.
     */
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
            '/SchoolERP/public/teacher/profile'
            . '#password-security'
        );
    }

    $this->session->flash(
        'success',
        'Your password has been changed successfully.'
    );

    return $this->redirect(
        '/SchoolERP/public/teacher/profile'
        . '#password-security'
    );
}
}