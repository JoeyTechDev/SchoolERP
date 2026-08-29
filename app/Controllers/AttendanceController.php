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
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        AttendanceRepository $attendance,
        ClassroomRepository $classrooms,
        StudentRepository $students,
        AcademicSessionRepository $sessions,
        TermRepository $terms
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
    }

    /**
     * Display the daily attendance form.
     */
    public function index(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $classrooms = $this->classrooms->allOrdered();
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
            $currentSession = $this->sessions->current();

            if ($currentSession !== null) {
                $sessionId = (int) $currentSession->id;
            }
        }

        /*
         * Validate the requested date before using it.
         */
        if (!$this->isValidDate($attendanceDate)) {
            $attendanceDate = date('Y-m-d');
        }

        $students = [];

        $existingAttendance = [];

        if ($classroomId > 0) {
            if (
                $this->classrooms->find(
                    $classroomId
                ) === null
            ) {
                return Response::notFound();
            }

            $students = $this->students->inClassroom(
                $classroomId
            );

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
         * Validate classroom.
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
        if (!$this->isValidDate($attendanceDate)) {
            return $this->attendanceError(
                'Please provide a valid attendance date.'
            );
        }

        /*
         * Load students belonging to the selected classroom.
         */
        $students = $this->students->inClassroom(
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
         * Save attendance for each student in the classroom.
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

            if (!in_array(
                $status,
                $allowedStatuses,
                true
            )) {
                return $this->attendanceError(
                    'An invalid attendance status was submitted.'
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
            if (mb_strlen($remark) > 255) {
                $remark = mb_substr(
                    $remark,
                    0,
                    255
                );
            }

            $existing = $this->attendance
                ->findForStudentDate(
                    $studentId,
                    $attendanceDate,
                    $sessionId,
                    $termId
                );

            $data = [
                'student_id' => $studentId,
                'academic_session_id' => $sessionId,
                'term_id' => $termId,
                'attendance_date' => $attendanceDate,
                'status' => $status,
                'remarks' => $remark !== ''
                    ? $remark
                    : null,
            ];

            if ($existing !== null) {
                $updated = $this->attendance
                    ->updateAttendance(
                        (int) $existing->id,
                        [
                            'status' => $status,
                            'remarks' => $remark !== ''
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
            . urlencode($attendanceDate);
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
        $parsed = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $date
        );

        if ($parsed === false) {
            return false;
        }

        return $parsed->format('Y-m-d') === $date;
    }
}