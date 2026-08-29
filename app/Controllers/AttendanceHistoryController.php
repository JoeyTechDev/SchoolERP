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
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        AttendanceRepository $attendance,
        StudentRepository $students,
        AcademicSessionRepository $sessions,
        TermRepository $terms,
        ClassroomRepository $classrooms
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
    }

    /**
     * Display student attendance history.
     */
    public function index(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $students = $this->students->allOrdered();

        /*
         * Use all sessions and terms so historical records
         * remain accessible.
         */
        $sessions = $this->sessions->allOrdered();
        $terms = $this->terms->allOrdered();

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
         * Default to the current session.
         */
        if ($sessionId === 0) {
            $currentSession = $this->sessions->current();

            if ($currentSession !== null) {
                $sessionId = (int) $currentSession->id;
            }
        }

        $history = [];
        $summary = null;
        $student = null;
        $classroom = null;

        if (
            $studentId > 0
            && $sessionId > 0
            && $termId > 0
        ) {
            $student = $this->students->find(
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
             * Load classroom information.
             */
            if ($student->classroom_id !== null) {
                $classroom = $this->classrooms->find(
                    (int) $student->classroom_id
                );
            }

            /*
             * Load dated attendance records.
             */
            $history = $this->attendance->forStudent(
                $studentId,
                $sessionId,
                $termId
            );

            /*
             * Calculate attendance summary.
             */
            $summary = $this->attendance->summaryForStudent(
                $studentId,
                $sessionId,
                $termId
            );
        }

        return $this->view(
            'attendance-history.index',
            [
                'title' => 'Attendance History',
                'students' => $students,
                'sessions' => $sessions,
                'terms' => $terms,
                'history' => $history,
                'summary' => $summary,
                'student' => $student,
                'classroom' => $classroom,
                'studentId' => $studentId,
                'sessionId' => $sessionId,
                'termId' => $termId,
            ]
        );
    }
}