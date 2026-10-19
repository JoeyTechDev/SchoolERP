<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicResultRepository;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\AttendanceRepository;
use SchoolERP\Repositories\ParentStudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Services\ParentAuthorizationService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

final class ParentPortalController extends Controller
{
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        private ParentAuthorizationService $parentAuthorization,
        private ParentStudentRepository $parentStudents,
        private AcademicResultRepository $academicResults,
        private AttendanceRepository $attendance,
        private AcademicSessionRepository $academicSessions,
        private TermRepository $terms,
        private SubjectRepository $subjects
    ) {
        parent::__construct($views, $session);
    }

    /**
     * Display the authenticated parent's dashboard.
     */
    public function dashboard(): Response
    {
        $authorizationError =
            $this->parentAuthorization->authorizationError();

        if ($authorizationError !== null) {
            return Response::make(
                $authorizationError,
                403
            );
        }

        return $this->view(
            'parent.dashboard',
            [
                'students' =>
                    $this->parentAuthorization->currentStudents(),
            ]
        );
    }

    /**
     * Display a linked student's profile, academic results,
     * and attendance information.
     */
    public function student(int $id): Response
    {
        /*
         * Verify that the current user is an authorized parent.
         */
        $authorizationError =
            $this->parentAuthorization->authorizationError();

        if ($authorizationError !== null) {
            return Response::make(
                $authorizationError,
                403
            );
        }

        /*
         * A parent may access only their own linked students.
         */
        if (
            !$this->parentAuthorization->canAccessStudent($id)
        ) {
            return Response::notFound();
        }

        $parentUserId =
            $this->parentAuthorization->currentUserId();

        $student = $this->parentStudents->studentForParent(
            $parentUserId,
            $id
        );

        if ($student === null) {
            return Response::notFound();
        }

        /*
         * Load the available academic periods.
         */
        $sessions = $this->academicSessions->allOrdered();
        $terms = $this->terms->allOrdered();
        $subjects = $this->subjects->allOrdered();

        /*
         * Prefer the current active academic session.
         * If none is configured, use the first available session.
         */
        $currentSession = $this->academicSessions->current();

        $sessionId = $currentSession !== null
            ? (int) $currentSession->id
            : (int) ($sessions[0]['id'] ?? 0);

        /*
         * Use the first configured term as the initial term.
         */
        $termId = (int) ($terms[0]['id'] ?? 0);

        /*
         * Retrieve records only for this authorized student
         * and the selected academic period.
         */
        $results = [];
        $attendanceHistory = [];

        $attendanceSummary = [
            'total_days' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'attendance_rate' => 0.0,
        ];

        if ($sessionId > 0 && $termId > 0) {
            $results = $this->academicResults->forStudent(
                $id,
                $sessionId,
                $termId
            );

            $attendanceHistory = $this->attendance->forStudent(
                $id,
                $sessionId,
                $termId
            );

            $attendanceSummary =
                $this->attendance->summaryForStudent(
                    $id,
                    $sessionId,
                    $termId
                );
        }

        return $this->view(
            'parent.student',
            [
                'student' => $student,
                'sessions' => $sessions,
                'terms' => $terms,
                'subjects' => $subjects,
                'results' => $results,
                'attendanceHistory' => $attendanceHistory,
                'attendanceSummary' => $attendanceSummary,
                'sessionId' => $sessionId,
                'termId' => $termId,
            ]
        );
    }
}
