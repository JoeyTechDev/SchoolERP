<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use RuntimeException;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\ReportCardSummaryRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Services\ReportCardService;
use SchoolERP\Services\TeacherAuthorizationService;
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
     * Teacher authorization service.
     */
    private TeacherAuthorizationService $authorization;

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
        ReportCardSummaryRepository $summaries,
        TeacherAuthorizationService $authorization
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
        $this->authorization = $authorization;
    }

    /**
     * Display a student report card.
     */
    public function index(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        /*
         * Administrators can see every student.
         *
         * Teachers only see students they are authorized
         * to manage.
         */
        $allStudents = $this->students->allOrdered();

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
         * Use all sessions and terms so historical reports
         * remain accessible.
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
         * Default to current academic session.
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
         * IMPORTANT SECURITY BOUNDARY:
         *
         * A Teacher cannot retrieve another student's
         * report by manually changing student_id in the URL.
         */
        if (
            $studentId > 0
            && $this->authorization->isTeacher()
            && !$this->authorization->canManageStudent(
                $studentId
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to access this student\'s report card.',
                403
            );
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
                'title' =>
                    'Student Report Card',

                'students' =>
                    $students,

                'sessions' =>
                    $sessions,

                'terms' =>
                    $terms,

                'report' =>
                    $report,

                'studentId' =>
                    $studentId,

                'sessionId' =>
                    $sessionId,

                'termId' =>
                    $termId,
            ]
        );
    }

    /**
     * Save report-card remarks and promotion status.
     *
     * Administrator:
     * - Can update class-teacher remark.
     * - Can update principal remark.
     * - Can update promotion status.
     *
     * Teacher:
     * - Can update class-teacher remark only.
     * - Must be assigned to the student.
     */
    public function saveSummary(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

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

        /*
         * IMPORTANT SECURITY BOUNDARY:
         *
         * A Teacher cannot submit a report-card summary
         * for an unassigned student, even by crafting a POST
         * request manually.
         */
        if (
            $this->authorization->isTeacher()
            && !$this->authorization->canManageStudent(
                $studentId
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to update this student\'s report card.',
                403
            );
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

        $promotionStatus =
            (string) $request->input(
                'promotion_status',
                'pending'
            );

        /*
         * Limit remarks to a reasonable size.
         */
        if (
            mb_strlen(
                $teacherRemark
            ) > 2000
        ) {
            $teacherRemark =
                mb_substr(
                    $teacherRemark,
                    0,
                    2000
                );
        }

        if (
            mb_strlen(
                $principalRemark
            ) > 2000
        ) {
            $principalRemark =
                mb_substr(
                    $principalRemark,
                    0,
                    2000
                );
        }

        /*
         * Administrator branch.
         */
        if ($roleId === 1) {
            $allowedPromotionStatuses = [
                'pending',
                'promoted',
                'not_promoted',
            ];

            if (
                !in_array(
                    $promotionStatus,
                    $allowedPromotionStatuses,
                    true
                )
            ) {
                $promotionStatus =
                    'pending';
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
             * Only set the administrator as class teacher
             * when no class-teacher has already been assigned.
             */
            $existing =
                $this->summaries->findForStudent(
                    $studentId,
                    $sessionId,
                    $termId
                );

            if (
                $existing === null
                && $userId > 0
            ) {
                $data[
                    'class_teacher_id'
                ] = $userId;
            }

        } else {
            /*
             * Teacher branch.
             *
             * Principal remark and promotion status are
             * deliberately ignored here. This remains true
             * even when a malicious request submits those
             * fields manually.
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

    /**
     * Display a clean printable report card.
     */
    public function print(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1]);

        if ($forbidden !== null) {
            return $forbidden;
        }

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

        if (
            $studentId <= 0
            || $sessionId <= 0
            || $termId <= 0
        ) {
            return Response::make(
                'Student, academic session, and term are required.',
                400
            );
        }

        /*
         * Prevent a Teacher from printing another
         * student's report by manually changing student_id.
         */
        if (
            $this->authorization->isTeacher()
            && !$this->authorization->canManageStudent(
                $studentId
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to print this student\'s report card.',
                403
            );
        }

        try {
            $report =
                $this->reports->build(
                    $studentId,
                    $sessionId,
                    $termId
                );
        } catch (
            RuntimeException $exception
        ) {
            return Response::make(
                $exception->getMessage(),
                404
            );
        }

        return $this->view(
            'report-card.print',
            [
                'title' =>
                    'Printable Report Card',

                'report' =>
                    $report,
            ]
        );
    }
}