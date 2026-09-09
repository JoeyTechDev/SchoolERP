<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

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

final class AttendanceHistoryController extends Controller
{
    /**
     * Attendance repository.
     */
    private AttendanceRepository $attendance;

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
     * Classroom repository.
     */
    private ClassroomRepository $classrooms;

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
        StudentRepository $students,
        AcademicSessionRepository $sessions,
        TermRepository $terms,
        ClassroomRepository $classrooms,
        TeacherAuthorizationService $authorization
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->attendance = $attendance;
        $this->students = $students;
        $this->sessions = $sessions;
        $this->terms = $terms;
        $this->classrooms = $classrooms;
        $this->authorization = $authorization;
    }

    /**
     * Display student attendance history.
     */
    public function index(
        Request $request
    ): Response {
        /*
         * Administrators and Teachers may access attendance
         * history, but Teachers are restricted to assigned
         * students.
         */
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        /*
         * Load all students first for Administrators.
         *
         * Teachers receive only students they are authorized
         * to manage.
         */
        $allStudents =
            $this->students->allOrdered();

        $students = $allStudents;

        if (
            $this->authorization->isTeacher()
        ) {
            $students = array_values(
                array_filter(
                    $allStudents,
                    function (
                        array $student
                    ): bool {
                        $studentId = (int) (
                            $student['id'] ?? 0
                        );

                        return $studentId > 0
                            && $this->authorization
                                ->canManageStudent(
                                    $studentId
                                );
                    }
                )
            );
        }

        /*
         * Use all sessions and terms so historical
         * attendance records remain accessible.
         */
        $sessions =
            $this->sessions->allOrdered();

        $terms =
            $this->terms->allOrdered();

        $studentId = max(
            0,
            (int) $request->get(
                'student_id',
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

        $history = [];

        $summary = null;

        $student = null;

        $classroom = null;

        /*
         * Only load attendance data when all required
         * filters are present.
         */
        if (
            $studentId > 0
            && $sessionId > 0
            && $termId > 0
        ) {
            /*
             * Load the requested student.
             */
            $student =
                $this->students->find(
                    $studentId
                );

            if ($student === null) {
                $this->session->flash(
                    'error',
                    'Student not found.'
                );

                return $this->redirect(
                    '/SchoolERP/public/attendance/history'
                );
            }

            /*
             * IMPORTANT SECURITY CHECK:
             *
             * A Teacher must not be able to bypass the
             * student dropdown by manually changing
             * student_id in the URL.
             */
            if (
                $this->authorization->isTeacher()
                && !$this->authorization
                    ->canManageStudent(
                        $studentId
                    )
            ) {
                return Response::make(
                    '403 Forbidden - You are not authorized to access this student\'s attendance history.',
                    403
                );
            }

            /*
             * Load classroom information.
             */
            $classroomId = (int) (
                $student->classroom_id ?? 0
            );

            if ($classroomId > 0) {
                $classroom =
                    $this->classrooms->find(
                        $classroomId
                    );
            }

            /*
             * Retrieve only this student's attendance
             * for the requested session and term.
             */
            $history =
                $this->attendance->forStudent(
                    $studentId,
                    $sessionId,
                    $termId
                );

            /*
             * Calculate this student's attendance summary.
             */
            $summary =
                $this->attendance
                    ->summaryForStudent(
                        $studentId,
                        $sessionId,
                        $termId
                    );
        }

        return $this->view(
            'attendance-history.index',
            [
                'title' =>
                    'Attendance History',

                'students' =>
                    $students,

                'sessions' =>
                    $sessions,

                'terms' =>
                    $terms,

                'history' =>
                    $history,

                'summary' =>
                    $summary,

                'student' =>
                    $student,

                'classroom' =>
                    $classroom,

                'studentId' =>
                    $studentId,

                'sessionId' =>
                    $sessionId,

                'termId' =>
                    $termId,
            ]
        );
    }
}