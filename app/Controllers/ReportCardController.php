<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\ReportCardSummaryRepository;
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
     * Report card summary repository.
     */
    private ReportCardSummaryRepository $summaries;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        ReportCardService $reports,
        StudentRepository $students,
        AcademicSessionRepository $sessions,
        TermRepository $terms,
        ReportCardSummaryRepository $summaries
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->reports = $reports;
        $this->students = $students;
        $this->sessions = $sessions;
        $this->terms = $terms;
        $this->summaries = $summaries;
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
         * Default to current academic session.
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
            } catch (\RuntimeException $exception) {
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

    /**
     * Save report-card remarks and promotion status.
     *
     * Administrator:
     * - Can update both remarks.
     * - Can update promotion status.
     *
     * Teacher:
     * - Can update class-teacher remark only.
     */
    public function saveSummary(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $studentId = (int) $request->input(
            'student_id',
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

        $student = $this->students->find(
            $studentId
        );

        if ($student === null) {
            return Response::notFound();
        }

        if (
            $this->sessions->find(
                $sessionId
            ) === null
        ) {
            return Response::notFound();
        }

        if (
            $this->terms->find(
                $termId
            ) === null
        ) {
            return Response::notFound();
        }

        $userId = (int) $this->session->get(
            'user_id',
            0
        );

        $roleId = (int) $this->session->get(
            'role_id',
            0
        );

        $teacherRemark = trim(
            (string) $request->input(
                'class_teacher_remark',
                ''
            )
        );

        $principalRemark = trim(
            (string) $request->input(
                'principal_remark',
                ''
            )
        );

        $promotionStatus = (
            (string) $request->input(
                'promotion_status',
                'pending'
            )
        );

        /*
         * Limit remarks to a reasonable size.
         */
        if (mb_strlen($teacherRemark) > 2000) {
            $teacherRemark = mb_substr(
                $teacherRemark,
                0,
                2000
            );
        }

        if (mb_strlen($principalRemark) > 2000) {
            $principalRemark = mb_substr(
                $principalRemark,
                0,
                2000
            );
        }

        /*
         * Only administrators can modify principal remarks
         * and promotion status.
         */
        if ($roleId === 1) {
            $allowedPromotionStatuses = [
                'pending',
                'promoted',
                'not_promoted',
            ];

            if (!in_array(
                $promotionStatus,
                $allowedPromotionStatuses,
                true
            )) {
                $promotionStatus = 'pending';
            }

            $data = [
                'class_teacher_remark' =>
                    $teacherRemark !== ''
                        ? $teacherRemark
                        : null,

                'principal_remark' =>
                    $principalRemark !== ''
                        ? $principalRemark
                        : null,

                'promotion_status' =>
                    $promotionStatus,

                'principal_id' =>
                    $userId > 0
                        ? $userId
                        : null,
            ];

            /*
             * Only set the administrator as class teacher if
             * a teacher has not already been assigned.
             */
            $existing = $this->summaries->findForStudent(
                $studentId,
                $sessionId,
                $termId
            );

            if (
                $existing === null
                && $userId > 0
            ) {
                $data['class_teacher_id'] = $userId;
            }

        } else {
            /*
             * Teacher can modify only the class-teacher remark.
             */
            $data = [
                'class_teacher_remark' =>
                    $teacherRemark !== ''
                        ? $teacherRemark
                        : null,

                'class_teacher_id' =>
                    $userId > 0
                        ? $userId
                        : null,
            ];
        }

        $this->summaries->saveForStudent(
            $studentId,
            $sessionId,
            $termId,
            $data
        );

        $this->session->flash(
            'success',
            'Report card information saved successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/report-card'
            . '?student_id='
            . $studentId
            . '&academic_session_id='
            . $sessionId
            . '&term_id='
            . $termId
        );
    }
}