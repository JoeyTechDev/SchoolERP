<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use PDOException;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\AcademicResultRepository;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Services\GradeService;
use SchoolERP\Services\ResultCalculationService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\Validation\Validator;
use SchoolERP\View\ViewFactory;

final class AcademicResultController extends Controller
{
    /**
     * Academic result repository.
     */
    private AcademicResultRepository $results;

    /**
     * Student repository.
     */
    private StudentRepository $students;

    /**
     * Subject repository.
     */
    private SubjectRepository $subjects;

    /**
     * Academic session repository.
     */
    private AcademicSessionRepository $sessions;

    /**
     * Term repository.
     */
    private TermRepository $terms;

    /**
     * Result calculation service.
     */
    private ResultCalculationService $calculator;

    /**
     * Grade service.
     */
    private GradeService $grader;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        AcademicResultRepository $results,
        StudentRepository $students,
        SubjectRepository $subjects,
        AcademicSessionRepository $sessions,
        TermRepository $terms,
        ResultCalculationService $calculator,
        GradeService $grader
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->results = $results;
        $this->students = $students;
        $this->subjects = $subjects;
        $this->sessions = $sessions;
        $this->terms = $terms;
        $this->calculator = $calculator;
        $this->grader = $grader;
    }

    /**
     * Display academic results.
     *
     * Optional query parameters:
     * student_id
     * academic_session_id
     * term_id
     */
    public function index(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $students = $this->students->allOrdered();
        $subjects = $this->subjects->active();
        $sessions = $this->sessions->active();
        $terms = $this->terms->active();

        $studentId = max(
            0,
            (int) $request->get('student_id', 0)
        );

        $sessionId = max(
            0,
            (int) $request->get('academic_session_id', 0)
        );

        $termId = max(
            0,
            (int) $request->get('term_id', 0)
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

        $results = [];

        if (
            $studentId > 0
            && $sessionId > 0
            && $termId > 0
        ) {
            $results = $this->results->forStudent(
                $studentId,
                $sessionId,
                $termId
            );
        }

        return $this->view(
            'academic-results.index',
            [
                'title' => 'Academic Results',
                'students' => $students,
                'subjects' => $subjects,
                'sessions' => $sessions,
                'terms' => $terms,
                'results' => $results,
                'studentId' => $studentId,
                'sessionId' => $sessionId,
                'termId' => $termId,
            ]
        );
    }

    /**
     * Show the create result form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $currentSession = $this->sessions->current();

        return $this->view(
            'academic-results.create',
            [
                'title' => 'Enter Academic Result',
                'students' => $this->students->allOrdered(),
                'subjects' => $this->subjects->active(),
                'sessions' => $this->sessions->active(),
                'terms' => $this->terms->active(),
                'currentSession' => $currentSession,
            ]
        );
    }

    /**
     * Store a new academic result.
     */
    public function store(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $data = [
            'student_id' => (int) $request->input(
                'student_id',
                0
            ),

            'subject_id' => (int) $request->input(
                'subject_id',
                0
            ),

            'academic_session_id' => (int) $request->input(
                'academic_session_id',
                0
            ),

            'term_id' => (int) $request->input(
                'term_id',
                0
            ),

            'ca_score' => (int) $request->input(
                'ca_score',
                0
            ),

            'exam_score' => (int) $request->input(
                'exam_score',
                0
            ),
        ];

        $validator = Validator::make(
            $data,
            [
                'student_id' => 'required|integer|min:1',
                'subject_id' => 'required|integer|min:1',
                'academic_session_id' => 'required|integer|min:1',
                'term_id' => 'required|integer|min:1',
                'ca_score' => 'required|integer|min:0|max:30',
                'exam_score' => 'required|integer|min:0|max:70',
            ]
        );

        if ($validator->fails()) {
            return $this->resultValidationError(
                $data,
                $validator->errors(),
                '/SchoolERP/public/academic-results/create'
            );
        }

        if (
            $this->students->find(
                $data['student_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'student_id' => 'The selected student does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        if (
            $this->subjects->find(
                $data['subject_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'subject_id' => 'The selected subject does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        if (
            $this->sessions->find(
                $data['academic_session_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'academic_session_id' => 'The selected academic session does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        if (
            $this->terms->find(
                $data['term_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'term_id' => 'The selected term does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        $totalScore = $this->calculator->total(
            $data['ca_score'],
            $data['exam_score']
        );

        $data['total_score'] = $totalScore;
        $data['grade'] = $this->grader->grade(
            $totalScore
        );
        $data['remark'] = $this->grader->remark(
            $totalScore
        );

        try {
            $this->results->create(
                $data
            );
        } catch (PDOException $exception) {
            if ($this->isDuplicateException($exception)) {
                return $this->resultValidationError(
                    $data,
                    [
                        'student_id' => 'A result already exists for this student, subject, session, and term.',
                    ],
                    '/SchoolERP/public/academic-results/create'
                );
            }

            throw $exception;
        }

        $this->session->flash(
            'success',
            'Academic result saved successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-results'
        );
    }

/**
 * Show the edit result form.
 */
public function edit(
    int $id
): Response {
    $forbidden = $this->requireRole([1, 2]);

    if ($forbidden !== null) {
        return $forbidden;
    }

    $result = $this->results->find($id);

    if ($result === null) {
        return Response::notFound();
    }

    return $this->view(
        'academic-results.edit',
        [
            'title' => 'Edit Academic Result',
            'result' => $result,

            /*
             * Use all records here so historical results remain editable
             * even when the related subject/session/term is inactive.
             */
            'students' => $this->students->allOrdered(),
            'subjects' => $this->subjects->allOrdered(),
            'sessions' => $this->sessions->allOrdered(),
            'terms' => $this->terms->allOrdered(),
        ]
    );
}

    /**
     * Update an academic result.
     */
    public function update(
        Request $request,
        int $id
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $result = $this->results->find($id);

        if ($result === null) {
            return Response::notFound();
        }

        $data = [
            'student_id' => (int) $request->input(
                'student_id',
                0
            ),

            'subject_id' => (int) $request->input(
                'subject_id',
                0
            ),

            'academic_session_id' => (int) $request->input(
                'academic_session_id',
                0
            ),

            'term_id' => (int) $request->input(
                'term_id',
                0
            ),

            'ca_score' => (int) $request->input(
                'ca_score',
                0
            ),

            'exam_score' => (int) $request->input(
                'exam_score',
                0
            ),
        ];

        $validator = Validator::make(
            $data,
            [
                'student_id' => 'required|integer|min:1',
                'subject_id' => 'required|integer|min:1',
                'academic_session_id' => 'required|integer|min:1',
                'term_id' => 'required|integer|min:1',
                'ca_score' => 'required|integer|min:0|max:30',
                'exam_score' => 'required|integer|min:0|max:70',
            ]
        );

        if ($validator->fails()) {
            return $this->resultValidationError(
                $data,
                $validator->errors(),
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        if (
            $this->students->find(
                $data['student_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'student_id' => 'The selected student does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        if (
            $this->subjects->find(
                $data['subject_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'subject_id' => 'The selected subject does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        if (
            $this->sessions->find(
                $data['academic_session_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'academic_session_id' => 'The selected academic session does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        if (
            $this->terms->find(
                $data['term_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'term_id' => 'The selected term does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        $totalScore = $this->calculator->total(
            $data['ca_score'],
            $data['exam_score']
        );

        $data['total_score'] = $totalScore;
        $data['grade'] = $this->grader->grade(
            $totalScore
        );
        $data['remark'] = $this->grader->remark(
            $totalScore
        );

        try {
            $updated = $this->results->updateResult(
                $id,
                $data
            );
        } catch (PDOException $exception) {
            if ($this->isDuplicateException($exception)) {
                return $this->resultValidationError(
                    $data,
                    [
                        'student_id' => 'A result already exists for this student, subject, session, and term.',
                    ],
                    '/SchoolERP/public/academic-results/'
                    . $id
                    . '/edit'
                );
            }

            throw $exception;
        }

        if (!$updated) {
            $this->session->flash(
                'error',
                'Unable to update academic result.'
            );

            return $this->redirect(
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        $this->session->flash(
            'success',
            'Academic result updated successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-results'
        );
    }

    /**
     * Delete an academic result.
     */
    public function destroy(
        int $id
    ): Response {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $result = $this->results->find($id);

        if ($result === null) {
            return Response::notFound();
        }

        if (!$this->results->delete($id)) {
            $this->session->flash(
                'error',
                'Unable to delete academic result.'
            );

            return $this->redirect(
                '/SchoolERP/public/academic-results'
            );
        }

        $this->session->flash(
            'success',
            'Academic result deleted successfully.'
        );

        return $this->redirect(
            '/SchoolERP/public/academic-results'
        );
    }

    /**
     * Store result form errors and old input.
     *
     * @param array<string,mixed> $data
     * @param array<string,mixed> $errors
     */
    private function resultValidationError(
        array $data,
        array $errors,
        string $redirect
    ): Response {
        $this->session->flash(
            '_old_input',
            $data
        );

        $this->session->flash(
            '_errors',
            $errors
        );

        return $this->redirect(
            $redirect
        );
    }

    /**
     * Determine whether an exception is a duplicate-key violation.
     */
    private function isDuplicateException(
        PDOException $exception
    ): bool {
        return $exception->getCode() === '23000'
            || str_contains(
                strtolower(
                    $exception->getMessage()
                ),
                'duplicate'
            );
    }
}