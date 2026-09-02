<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use DateTimeImmutable;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\AttendanceRepository;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Services\TeacherAuthorizationService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

final class AttendanceController extends Controller
{
    /**
     * Attendance repository.
     */
    private AttendanceRepository $attendance;

    /**
     * Classroom repository.
     */
    private ClassroomRepository $classrooms;

    /**
     * Student repository.
     */
    private StudentRepository $students;

    /**
     * Academic session repository.
     */
    private AcademicSessionRepository $sessions;

    /**
     * Term repository.
     */
    private TermRepository $terms;

    /**
     * Teacher authorization service.
     */
    private TeacherAuthorizationService $authorization;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        AttendanceRepository $attendance,
        ClassroomRepository $classrooms,
        StudentRepository $students,
        AcademicSessionRepository $sessions,
        TermRepository $terms,
        TeacherAuthorizationService $authorization
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->attendance = $attendance;
        $this->classrooms = $classrooms;
        $this->students = $students;
        $this->sessions = $sessions;
        $this->terms = $terms;
        $this->authorization = $authorization;
    }

    /**
     * Display the daily attendance form.
     */
    public function index(
        Request $request
    ): Response {
        /*
         * Both administrators and teachers can access attendance.
         * Classroom-level access is checked separately below.
         */
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        /*
         * Get available classrooms.
         */
        $allClassrooms =
            $this->classrooms->allOrdered();

        $classrooms = $allClassrooms;

        /*
         * Teachers may only see classrooms they are assigned to.
         * Administrators retain unrestricted access.
         */
        if ($this->authorization->isTeacher()) {

            $classrooms = array_values(
                array_filter(
                    $allClassrooms,
                    function (
                        array $classroom
                    ): bool {
                        $id = (int) (
                            $classroom['id'] ?? 0
                        );

                        return $id > 0
                            && $this->authorization
                                ->canAccessClassroom(
                                    $id
                                );
                    }
                )
            );
        }

        $sessions = $this->sessions->active();
        $terms = $this->terms->active();

        $classroomId = max(
            0,
            (int) $request->get(
                'classroom_id',
                0
            )
        );

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

        $attendanceDate = trim(
            (string) $request->get(
                'attendance_date',
                date('Y-m-d')
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
         * Validate requested date.
         */
        if (
            !$this->isValidDate(
                $attendanceDate
            )
        ) {
            $attendanceDate = date(
                'Y-m-d'
            );
        }

        /*
         * Teacher classroom authorization.
         *
         * A teacher who does not have a classroom assignment
         * receives a normal 403 rather than an empty attendance
         * screen that could be confusing.
         */
        if (
            $this->authorization->isTeacher()
            && $classroomId > 0
            && !$this->authorization->canAccessClassroom(
                $classroomId
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not assigned to this classroom.',
                403
            );
        }

        /*
         * If a teacher has no assigned classrooms, there is
         * nothing they are allowed to record attendance for.
         */
        if (
            $this->authorization->isTeacher()
            && $classrooms === []
        ) {
            $this->session->flash(
                'error',
                'You do not have any classroom assignments.'
            );

            return $this->view(
                'attendance.index',
                [
                    'title' => 'Daily Attendance',
                    'classrooms' => [],
                    'sessions' => $sessions,
                    'terms' => $terms,
                    'students' => [],
                    'existingAttendance' => [],
                    'classroomId' => 0,
                    'sessionId' => $sessionId,
                    'termId' => $termId,
                    'attendanceDate' => $attendanceDate,
                ]
            );
        }

        $students = [];
        $existingAttendance = [];

        if ($classroomId > 0) {

            /*
             * Validate classroom existence.
             */
            if (
                $this->classrooms->find(
                    $classroomId
                ) === null
            ) {
                return Response::notFound();
            }

            /*
             * Load students in the selected classroom.
             */
            $students =
                $this->students->inClassroom(
                    $classroomId
                );

            /*
             * Load existing attendance records.
             */
            if (
                $sessionId > 0
                && $termId > 0
            ) {
                $existingAttendance =
                    $this->attendance
                        ->forDateIndexedByStudent(
                            $attendanceDate,
                            $sessionId,
                            $termId
                        );
            }
        }

        return $this->view(
            'attendance.index',
            [
                'title' => 'Daily Attendance',
                'classrooms' => $classrooms,
                'sessions' => $sessions,
                'terms' => $terms,
                'students' => $students,
                'existingAttendance' => $existingAttendance,
                'classroomId' => $classroomId,
                'sessionId' => $sessionId,
                'termId' => $termId,
                'attendanceDate' => $attendanceDate,
            ]
        );
    }

    /**
     * Save daily attendance for a classroom.
     */
    public function store(
        Request $request
    ): Response {
        /*
         * Both administrators and teachers may submit attendance.
         */
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classroomId = (int) $request->input(
            'classroom_id',
            0
        );

        $sessionId = (int) $request->input(
            'academic_session_id',
            0
        );

        $termId = (int) $request->input(
            'term_id',
            0
        );

        $attendanceDate = trim(
            (string) $request->input(
                'attendance_date',
                ''
            )
        );

        $statuses = $request->input(
            'status',
            []
        );

        $remarks = $request->input(
            'remarks',
            []
        );

        if (!is_array($statuses)) {
            $statuses = [];
        }

        if (!is_array($remarks)) {
            $remarks = [];
        }

        /*
         * Validate classroom existence.
         */
        $classroom = $this->classrooms->find(
            $classroomId
        );

        if ($classroom === null) {
            return $this->attendanceError(
                'The selected classroom does not exist.'
            );
        }

        /*
         * IMPORTANT:
         *
         * Role authorization alone is not sufficient for teachers.
         * A teacher must actually be assigned to this classroom.
         */
        if (
            $this->authorization->isTeacher()
            && !$this->authorization
                ->canAccessClassroom(
                    $classroomId
                )
        ) {
            return Response::make(
                '403 Forbidden - You are not assigned to this classroom.',
                403
            );
        }

        /*
         * Validate academic session.
         */
        if (
            $this->sessions->find(
                $sessionId
            ) === null
        ) {
            return $this->attendanceError(
                'The selected academic session does not exist.'
            );
        }

        /*
         * Validate term.
         */
        if (
            $this->terms->find(
                $termId
            ) === null
        ) {
            return $this->attendanceError(
                'The selected term does not exist.'
            );
        }

        /*
         * Validate attendance date.
         */
        if (
            !$this->isValidDate(
                $attendanceDate
            )
        ) {
            return $this->attendanceError(
                'Please provide a valid attendance date.'
            );
        }

        /*
         * Load students belonging to the selected classroom.
         */
        $students =
            $this->students->inClassroom(
                $classroomId
            );

        if ($students === []) {

            $this->session->flash(
                'error',
                'This classroom has no students assigned to it.'
            );

            return $this->redirect(
                $this->attendanceUrl(
                    $classroomId,
                    $sessionId,
                    $termId,
                    $attendanceDate
                )
            );
        }

        /*
         * Valid attendance statuses.
         */
        $allowedStatuses = [
            'present',
            'absent',
            'late',
            'excused',
        ];

        /*
         * Save attendance for each student.
         */
        foreach ($students as $student) {

            $studentId = (int) (
                $student['id'] ?? 0
            );

            /*
             * Default new records to present.
             */
            $status = isset(
                $statuses[$studentId]
            )
                ? strtolower(
                    trim(
                        (string) $statuses[$studentId]
                    )
                )
                : 'present';

            if (
                !in_array(
                    $status,
                    $allowedStatuses,
                    true
                )
            ) {
                return $this->attendanceError(
                    'An invalid attendance status was submitted.'
                );
            }

            /*
             * Teacher-level student authorization.
             *
             * This is a second security boundary against a
             * manipulated POST request.
             */
            if (
                $this->authorization->isTeacher()
                && !$this->authorization
                    ->canManageStudent(
                        $studentId
                    )
            ) {
                return Response::make(
                    '403 Forbidden - You are not authorized to manage one or more students in this classroom.',
                    403
                );
            }

            $remark = isset(
                $remarks[$studentId]
            )
                ? trim(
                    (string) $remarks[$studentId]
                )
                : '';

            /*
             * Prevent excessively large remarks.
             */
            if (
                mb_strlen($remark) > 255
            ) {
                $remark = mb_substr(
                    $remark,
                    0,
                    255
                );
            }

            $existing =
                $this->attendance
                    ->findForStudentDate(
                        $studentId,
                        $attendanceDate,
                        $sessionId,
                        $termId
                    );

            $data = [
                'student_id' =>
                    $studentId,

                'academic_session_id' =>
                    $sessionId,

                'term_id' =>
                    $termId,

                'attendance_date' =>
                    $attendanceDate,

                'status' =>
                    $status,

                'remarks' =>
                    $remark !== ''
                        ? $remark
                        : null,
            ];

            if ($existing !== null) {

                $updated =
                    $this->attendance
                        ->updateAttendance(
                            (int) $existing->id,
                            [
                                'status' =>
                                    $status,

                                'remarks' =>
                                    $remark !== ''
                                        ? $remark
                                        : null,
                            ]
                        );

                if (!$updated) {

                    $this->session->flash(
                        'error',
                        'Unable to update attendance for one or more students.'
                    );

                    return $this->redirect(
                        $this->attendanceUrl(
                            $classroomId,
                            $sessionId,
                            $termId,
                            $attendanceDate
                        )
                    );
                }

                continue;
            }

            $this->attendance->create(
                $data
            );
        }

        $this->session->flash(
            'success',
            'Classroom attendance saved successfully.'
        );

        return $this->redirect(
            $this->attendanceUrl(
                $classroomId,
                $sessionId,
                $termId,
                $attendanceDate
            )
        );
    }

    /**
     * Build the attendance page URL.
     */
    private function attendanceUrl(
        int $classroomId,
        int $sessionId,
        int $termId,
        string $attendanceDate
    ): string {
        return '/SchoolERP/public/attendance'
            . '?classroom_id='
            . $classroomId
            . '&academic_session_id='
            . $sessionId
            . '&term_id='
            . $termId
            . '&attendance_date='
            . urlencode(
                $attendanceDate
            );
    }

    /**
     * Store an attendance error and redirect.
     */
    private function attendanceError(
        string $message
    ): Response {
        $this->session->flash(
            'error',
            $message
        );

        return $this->redirect(
            '/SchoolERP/public/attendance'
        );
    }

    /**
     * Validate a YYYY-MM-DD date.
     */
    private function isValidDate(
        string $date
    ): bool {
        $parsed =
            DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $date
            );

        if ($parsed === false) {
            return false;
        }

        return $parsed->format(
            'Y-m-d'
        ) === $date;
    }
}
