<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
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
 * and attendance for the selected academic period.
 */
public function student(
    Request $request,
    int $id
): Response {
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
    if (!$this->parentAuthorization->canAccessStudent($id)) {
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
     * Determine the default academic session.
     */
    $currentSession = $this->academicSessions->current();

    $defaultSessionId = $currentSession !== null
        ? (int) $currentSession->id
        : (int) ($sessions[0]['id'] ?? 0);

    /*
     * Determine the default term.
     */
    $defaultTermId = (int) ($terms[0]['id'] ?? 0);

    /*
     * Validate requested IDs against configured records.
     */
    $sessionExists = static function (
        int $requestedId
    ) use ($sessions): bool {
        foreach ($sessions as $session) {
            if (
                (int) ($session['id'] ?? 0)
                === $requestedId
            ) {
                return true;
            }
        }

        return false;
    };

    $termExists = static function (
        int $requestedId
    ) use ($terms): bool {
        foreach ($terms as $term) {
            if (
                (int) ($term['id'] ?? 0)
                === $requestedId
            ) {
                return true;
            }
        }

        return false;
    };

    /*
     * Read and validate the selected session.
     * An invalid or missing selection falls back to the default.
     */
    $requestedSessionId = max(
        0,
        (int) $request->get('academic_session_id', 0)
    );

    $sessionId = $requestedSessionId > 0
        && $sessionExists($requestedSessionId)
            ? $requestedSessionId
            : $defaultSessionId;

    /*
     * Read and validate the selected term.
     */
    $requestedTermId = max(
        0,
        (int) $request->get('term_id', 0)
    );

    $termId = $requestedTermId > 0
        && $termExists($requestedTermId)
            ? $requestedTermId
            : $defaultTermId;

    /*
     * Initialize empty records and attendance summary.
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

    /*
     * Query only when both academic period selections are valid.
     * The student ID comes from the authorized student route,
     * never from the query string.
     */
    if (
        $sessionId > 0
        && $termId > 0
        && $sessionExists($sessionId)
        && $termExists($termId)
    ) {
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
