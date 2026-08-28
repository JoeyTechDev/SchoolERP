<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use RuntimeException;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Services\ReportCardService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

final class ReportCardController extends Controller
{
    /**
     * Report card service.
     */
    private ReportCardService $reports;

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
        ReportCardService $reports,
        StudentRepository $students,
        AcademicSessionRepository $sessions,
        TermRepository $terms
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->reports = $reports;
        $this->students = $students;
        $this->sessions = $sessions;
        $this->terms = $terms;
    }

    /**
 * Display a student report card.
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
     * Use all sessions and terms so historical reports
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
     * Default to the current academic session.
     */
    if ($sessionId === 0) {
        $currentSession = $this->sessions->current();

        if ($currentSession !== null) {
            $sessionId = (int) $currentSession->id;
        }
    }

    $report = null;

    if (
        $studentId > 0
        && $sessionId > 0
        && $termId > 0
    ) {
        try {
            $report = $this->reports->build(
                $studentId,
                $sessionId,
                $termId
            );
        } catch (RuntimeException $exception) {
            $this->session->flash(
                'error',
                $exception->getMessage()
            );

            return $this->redirect(
                '/SchoolERP/public/report-card'
            );
        }
    }

    return $this->view(
        'report-card.index',
        [
            'title' => 'Student Report Card',
            'students' => $students,
            'sessions' => $sessions,
            'terms' => $terms,
            'report' => $report,
            'studentId' => $studentId,
            'sessionId' => $sessionId,
            'termId' => $termId,
        ]
    );
}
}
