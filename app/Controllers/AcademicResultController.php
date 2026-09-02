<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use PDOException;
use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Models\Student;
use SchoolERP\Repositories\AcademicResultRepository;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TermRepository;
use SchoolERP\Services\GradeService;
use SchoolERP\Services\ResultCalculationService;
use SchoolERP\Services\TeacherAuthorizationService;
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
     * Teacher authorization service.
     */
    private TeacherAuthorizationService $authorization;

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
        GradeService $grader,
        TeacherAuthorizationService $authorization
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
        $this->authorization = $authorization;
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

        /*
         * Load students.
         */
        $allStudents =
            $this->students->allOrdered();

        /*
         * Teachers may only see students in classrooms
         * they are assigned to.
         */
        $students =
            $this->filterStudentsForCurrentUser(
                $allStudents
            );

        /*
         * Load active subjects.
         */
        $allSubjects =
            $this->subjects->active();

        $sessions =
            $this->sessions->active();

        $terms =
            $this->terms->active();

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

        /*
         * A teacher must not be able to open another
         * classroom's student by manually changing the URL.
         */
        $selectedStudent = null;

        if ($studentId > 0) {

            $selectedStudent =
                $this->students->find(
                    $studentId
                );

            if ($selectedStudent === null) {
                return Response::notFound();
            }

            if (
                $this->authorization->isTeacher()
                && !$this->authorization
                    ->canManageStudent(
                        $studentId
                    )
            ) {
                return Response::make(
                    '403 Forbidden - You are not assigned to this student\'s classroom.',
                    403
                );
            }
        }

        /*
         * Restrict the subject list for a selected student
         * to the subjects the teacher is assigned to teach
         * in that student's classroom.
         */
        $subjects = $allSubjects;

        if (
            $this->authorization->isTeacher()
            && $selectedStudent !== null
        ) {
            $subjects =
                $this->filterSubjectsForStudent(
                    $selectedStudent,
                    $allSubjects
                );
        }

        $results = [];

        if (
            $studentId > 0
            && $sessionId > 0
            && $termId > 0
        ) {
            $results =
                $this->results->forStudent(
                    $studentId,
                    $sessionId,
                    $termId
                );
        }

        return $this->view(
            'academic-results.index',
            [
                'title' =>
                    'Academic Results',

                'students' =>
                    $students,

                'subjects' =>
                    $subjects,

                'sessions' =>
                    $sessions,

                'terms' =>
                    $terms,

                'results' =>
                    $results,

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
     * Show the create result form.
     */
    public function create(): Response
    {
        $forbidden = $this->requireRole([1, 2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $currentSession =
            $this->sessions->current();

        $allStudents =
            $this->students->allOrdered();

        $students =
            $this->filterStudentsForCurrentUser(
                $allStudents
            );

        $allSubjects =
            $this->subjects->active();

        /*
         * For a teacher, show only subjects that are assigned
         * in at least one of their classrooms.
         *
         * The final classroom/subject authorization is still
         * performed server-side in store().
         */
        $subjects =
            $this->filterSubjectsForCurrentUser(
                $allSubjects
            );

        return $this->view(
            'academic-results.create',
            [
                'title' =>
                    'Enter Academic Result',

                'students' =>
                    $students,

                'subjects' =>
                    $subjects,

                'sessions' =>
                    $this->sessions->active(),

                'terms' =>
                    $this->terms->active(),

                'currentSession' =>
                    $currentSession,
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
            'student_id' =>
                (int) $request->input(
                    'student_id',
                    0
                ),

            'subject_id' =>
                (int) $request->input(
                    'subject_id',
                    0
                ),

            'academic_session_id' =>
                (int) $request->input(
                    'academic_session_id',
                    0
                ),

            'term_id' =>
                (int) $request->input(
                    'term_id',
                    0
                ),

            'ca_score' =>
                (int) $request->input(
                    'ca_score',
                    0
                ),

            'exam_score' =>
                (int) $request->input(
                    'exam_score',
                    0
                ),
        ];

        $validator = Validator::make(
            $data,
            [
                'student_id' =>
                    'required|integer|min:1',

                'subject_id' =>
                    'required|integer|min:1',

                'academic_session_id' =>
                    'required|integer|min:1',

                'term_id' =>
                    'required|integer|min:1',

                'ca_score' =>
                    'required|integer|min:0|max:30',

                'exam_score' =>
                    'required|integer|min:0|max:70',
            ]
        );

        if ($validator->fails()) {
            return $this->resultValidationError(
                $data,
                $validator->errors(),
                '/SchoolERP/public/academic-results/create'
            );
        }

        /*
         * Validate student.
         */
        $student =
            $this->students->find(
                $data['student_id']
            );

        if ($student === null) {
            return $this->resultValidationError(
                $data,
                [
                    'student_id' =>
                        'The selected student does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        /*
         * Validate subject.
         */
        if (
            $this->subjects->find(
                $data['subject_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'subject_id' =>
                        'The selected subject does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        /*
         * Teacher authorization.
         *
         * The teacher must be assigned to BOTH:
         *
         * student classroom
         * selected subject
         */
        if (
            !$this->canManageStudentSubject(
                $student,
                $data['subject_id']
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to enter results for this student and subject.',
                403
            );
        }

        /*
         * Validate academic session.
         */
        if (
            $this->sessions->find(
                $data['academic_session_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'academic_session_id' =>
                        'The selected academic session does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        /*
         * Validate term.
         */
        if (
            $this->terms->find(
                $data['term_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'term_id' =>
                        'The selected term does not exist.',
                ],
                '/SchoolERP/public/academic-results/create'
            );
        }

        /*
         * Calculate total, grade, and remark.
         *
         * CA = 30
         * Exam = 70
         * Total = 100
         */
        $totalScore =
            $this->calculator->total(
                $data['ca_score'],
                $data['exam_score']
            );

        $data['total_score'] =
            $totalScore;

        $data['grade'] =
            $this->grader->grade(
                $totalScore
            );

        $data['remark'] =
            $this->grader->remark(
                $totalScore
            );

        try {

            $this->results->create(
                $data
            );

        } catch (PDOException $exception) {

            if (
                $this->isDuplicateException(
                    $exception
                )
            ) {
                return $this->resultValidationError(
                    $data,
                    [
                        'student_id' =>
                            'A result already exists for this student, subject, session, and term.',
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

        $result =
            $this->results->find(
                $id
            );

        if ($result === null) {
            return Response::notFound();
        }

        /*
         * Teacher authorization uses the result currently
         * stored in the database.
         */
        if (
            !$this->canManageResult(
                $result
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to edit this academic result.',
                403
            );
        }

        $allStudents =
            $this->students->allOrdered();

        $students =
            $this->filterStudentsForCurrentUser(
                $allStudents
            );

        $allSubjects =
            $this->subjects->allOrdered();

        /*
         * Historical results may refer to inactive subjects,
         * so the existing result's subject must remain available
         * on the edit page for administrators. Teachers only
         * receive subjects they are currently authorized to manage.
         */
        $subjects = $allSubjects;

        if (
            $this->authorization->isTeacher()
        ) {
            $student =
                $this->students->find(
                    (int) (
                        $result->student_id
                        ?? 0
                    )
                );

            if ($student === null) {
                return Response::notFound();
            }

            $subjects =
                $this->filterSubjectsForStudent(
                    $student,
                    $allSubjects
                );
        }

        return $this->view(
            'academic-results.edit',
            [
                'title' =>
                    'Edit Academic Result',

                'result' =>
                    $result,

                'students' =>
                    $students,

                'subjects' =>
                    $subjects,

                'sessions' =>
                    $this->sessions->allOrdered(),

                'terms' =>
                    $this->terms->allOrdered(),
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

        $result =
            $this->results->find(
                $id
            );

        if ($result === null) {
            return Response::notFound();
        }

        /*
         * First make sure the logged-in teacher is authorized
         * to modify the existing result.
         */
        if (
            !$this->canManageResult(
                $result
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to edit this academic result.',
                403
            );
        }

        $data = [
            'student_id' =>
                (int) $request->input(
                    'student_id',
                    0
                ),

            'subject_id' =>
                (int) $request->input(
                    'subject_id',
                    0
                ),

            'academic_session_id' =>
                (int) $request->input(
                    'academic_session_id',
                    0
                ),

            'term_id' =>
                (int) $request->input(
                    'term_id',
                    0
                ),

            'ca_score' =>
                (int) $request->input(
                    'ca_score',
                    0
                ),

            'exam_score' =>
                (int) $request->input(
                    'exam_score',
                    0
                ),
        ];

        $validator = Validator::make(
            $data,
            [
                'student_id' =>
                    'required|integer|min:1',

                'subject_id' =>
                    'required|integer|min:1',

                'academic_session_id' =>
                    'required|integer|min:1',

                'term_id' =>
                    'required|integer|min:1',

                'ca_score' =>
                    'required|integer|min:0|max:30',

                'exam_score' =>
                    'required|integer|min:0|max:70',
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

        /*
         * Validate student.
         */
        $student =
            $this->students->find(
                $data['student_id']
            );

        if ($student === null) {
            return $this->resultValidationError(
                $data,
                [
                    'student_id' =>
                        'The selected student does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        /*
         * Validate subject.
         */
        if (
            $this->subjects->find(
                $data['subject_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'subject_id' =>
                        'The selected subject does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        /*
         * Authorize the submitted student + subject combination.
         *
         * This protects against manually modifying hidden
         * form fields or changing the URL/request payload.
         */
        if (
            !$this->canManageStudentSubject(
                $student,
                $data['subject_id']
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to update results for this student and subject.',
                403
            );
        }

        /*
         * Validate academic session.
         */
        if (
            $this->sessions->find(
                $data['academic_session_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'academic_session_id' =>
                        'The selected academic session does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        /*
         * Validate term.
         */
        if (
            $this->terms->find(
                $data['term_id']
            ) === null
        ) {
            return $this->resultValidationError(
                $data,
                [
                    'term_id' =>
                        'The selected term does not exist.',
                ],
                '/SchoolERP/public/academic-results/'
                . $id
                . '/edit'
            );
        }

        /*
         * Recalculate all derived academic values.
         */
        $totalScore =
            $this->calculator->total(
                $data['ca_score'],
                $data['exam_score']
            );

        $data['total_score'] =
            $totalScore;

        $data['grade'] =
            $this->grader->grade(
                $totalScore
            );

        $data['remark'] =
            $this->grader->remark(
                $totalScore
            );

        try {

            $updated =
                $this->results->updateResult(
                    $id,
                    $data
                );

        } catch (PDOException $exception) {

            if (
                $this->isDuplicateException(
                    $exception
                )
            ) {
                return $this->resultValidationError(
                    $data,
                    [
                        'student_id' =>
                            'A result already exists for this student, subject, session, and term.',
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

        $result =
            $this->results->find(
                $id
            );

        if ($result === null) {
            return Response::notFound();
        }

        /*
         * Teachers may delete only results belonging to
         * their assigned classroom + subject combination.
         */
        if (
            !$this->canManageResult(
                $result
            )
        ) {
            return Response::make(
                '403 Forbidden - You are not authorized to delete this academic result.',
                403
            );
        }

        if (
            !$this->results->delete(
                $id
            )
        ) {
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
     * Determine whether the current user can manage
     * a result's existing student + subject combination.
     */
    private function canManageResult(
        object $result
    ): bool {
        if (
            $this->authorization->isAdmin()
        ) {
            return true;
        }

        if (
            !$this->authorization->isTeacher()
        ) {
            return false;
        }

        $studentId = (int) (
            $result->student_id ?? 0
        );

        $subjectId = (int) (
            $result->subject_id ?? 0
        );

        if (
            $studentId <= 0
            || $subjectId <= 0
        ) {
            return false;
        }

        $student =
            $this->students->find(
                $studentId
            );

        if ($student === null) {
            return false;
        }

        return $this->canManageStudentSubject(
            $student,
            $subjectId
        );
    }

    /**
     * Determine whether the current user can manage
     * a specific student + subject combination.
     */
    private function canManageStudentSubject(
        Student $student,
        int $subjectId
    ): bool {
        if (
            $this->authorization->isAdmin()
        ) {
            return true;
        }

        if (
            !$this->authorization->isTeacher()
        ) {
            return false;
        }

        $classroomId = (int) (
            $student->classroom_id
            ?? 0
        );

        if (
            $classroomId <= 0
        ) {
            return false;
        }

        return $this->authorization
            ->canManageSubject(
                $classroomId,
                $subjectId
            );
    }

    /**
     * Filter students according to the current user's role.
     *
     * Administrators receive all students.
     *
     * Teachers receive only students in assigned classrooms.
     *
     * @param array<int,array<string,mixed>> $students
     *
     * @return array<int,array<string,mixed>>
     */
    private function filterStudentsForCurrentUser(
        array $students
    ): array {
        if (
            $this->authorization->isAdmin()
        ) {
            return $students;
        }

        if (
            !$this->authorization->isTeacher()
        ) {
            return [];
        }

        return array_values(
            array_filter(
                $students,
                function (
                    array $student
                ): bool {
                    $studentId = (int) (
                        $student['id'] ?? 0
                    );

                    if ($studentId <= 0) {
                        return false;
                    }

                    return $this->authorization
                        ->canManageStudent(
                            $studentId
                        );
                }
            )
        );
    }

    /**
     * Filter subjects the current teacher teaches
     * in at least one assigned classroom.
     *
     * @param array<int,array<string,mixed>> $subjects
     *
     * @return array<int,array<string,mixed>>
     */
    private function filterSubjectsForCurrentUser(
        array $subjects
    ): array {
        if (
            $this->authorization->isAdmin()
        ) {
            return $subjects;
        }

        if (
            !$this->authorization->isTeacher()
        ) {
            return [];
        }

        /*
         * Get the current teacher's assigned classrooms
         * by checking the students already available to
         * the teacher.
         *
         * This avoids introducing another data-access path.
         */
        $students =
            $this->students->allOrdered();

        $classroomIds = [];

        foreach (
            $students as $student
        ) {
            $studentId = (int) (
                $student['id'] ?? 0
            );

            $classroomId = (int) (
                $student['classroom_id']
                ?? 0
            );

            if (
                $studentId <= 0
                || $classroomId <= 0
            ) {
                continue;
            }

            if (
                $this->authorization
                    ->canManageStudent(
                        $studentId
                    )
            ) {
                $classroomIds[
                    $classroomId
                ] = true;
            }
        }

        if ($classroomIds === []) {
            return [];
        }

        return array_values(
            array_filter(
                $subjects,
                function (
                    array $subject
                ) use (
                    $classroomIds
                ): bool {
                    $subjectId = (int) (
                        $subject['id'] ?? 0
                    );

                    if ($subjectId <= 0) {
                        return false;
                    }

                    foreach (
                        array_keys(
                            $classroomIds
                        ) as $classroomId
                    ) {
                        if (
                            $this->authorization
                                ->canManageSubject(
                                    (int) $classroomId,
                                    $subjectId
                                )
                        ) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );
    }

    /**
     * Filter subjects for a specific student.
     *
     * @param array<int,array<string,mixed>> $subjects
     *
     * @return array<int,array<string,mixed>>
     */
    private function filterSubjectsForStudent(
        Student $student,
        array $subjects
    ): array {
        if (
            $this->authorization->isAdmin()
        ) {
            return $subjects;
        }

        $classroomId = (int) (
            $student->classroom_id
            ?? 0
        );

        if ($classroomId <= 0) {
            return [];
        }

        return array_values(
            array_filter(
                $subjects,
                function (
                    array $subject
                ) use (
                    $classroomId
                ): bool {
                    $subjectId = (int) (
                        $subject['id'] ?? 0
                    );

                    return $subjectId > 0
                        && $this->authorization
                            ->canManageSubject(
                                $classroomId,
                                $subjectId
                            );
                }
            )
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
