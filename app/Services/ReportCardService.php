<?php

declare(strict_types=1);

namespace SchoolERP\Services;

use SchoolERP\Models\AcademicSession;
use SchoolERP\Models\Student;
use SchoolERP\Models\Term;
use SchoolERP\Repositories\AcademicResultRepository;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TermRepository;
use RuntimeException;

final class ReportCardService
{
    /**
     * Constructor.
     */
    public function __construct(
        private AcademicResultRepository $results,
        private StudentRepository $students,
        private SubjectRepository $subjects,
        private AcademicSessionRepository $sessions,
        private TermRepository $terms
    ) {
    }

    /**
     * Build a student's report card.
     *
     * @return array{
     *     student: Student,
     *     academic_session: AcademicSession,
     *     term: Term,
     *     results: array<int,array<string,mixed>>,
     *     total_score: int,
     *     average_score: float,
     *     result_count: int
     * }
     */
    public function build(
        int $studentId,
        int $academicSessionId,
        int $termId
    ): array {
        $student = $this->students->find(
            $studentId
        );

        if ($student === null) {
            throw new RuntimeException(
                'Student not found.'
            );
        }

        $academicSession = $this->sessions->find(
            $academicSessionId
        );

        if ($academicSession === null) {
            throw new RuntimeException(
                'Academic session not found.'
            );
        }

        $term = $this->terms->find(
            $termId
        );

        if ($term === null) {
            throw new RuntimeException(
                'Academic term not found.'
            );
        }

        /*
         * Use all subjects so historical results still display
         * correctly when a subject has later been deactivated.
         */
        $subjects = $this->subjects->allOrdered();

        $subjectLookup = [];

        foreach ($subjects as $subject) {
            $subjectLookup[(int) $subject['id']] = [
                'name' => (string) (
                    $subject['name'] ?? ''
                ),
                'code' => (string) (
                    $subject['code'] ?? ''
                ),
            ];
        }

        $records = $this->results->forStudent(
            $studentId,
            $academicSessionId,
            $termId
        );

        $reportResults = [];
        $totalScore = 0;
        $resultCount = 0;

        foreach ($records as $record) {
            $subjectId = (int) (
                $record['subject_id'] ?? 0
            );

            $score = (int) (
                $record['total_score'] ?? 0
            );

            $reportResults[] = [
                'id' => (int) (
                    $record['id'] ?? 0
                ),

                'subject_id' => $subjectId,

                'subject_name' => $subjectLookup[$subjectId]['name']
                    ?? 'Unknown Subject',

                'subject_code' => $subjectLookup[$subjectId]['code']
                    ?? '',

                'ca_score' => $record['ca_score']
                    ?? null,

                'exam_score' => $record['exam_score']
                    ?? null,

                'total_score' => $record['total_score']
                    ?? null,

                'grade' => (string) (
                    $record['grade'] ?? ''
                ),

                'remark' => (string) (
                    $record['remark'] ?? ''
                ),
            ];

            $totalScore += $score;
            $resultCount++;
        }

        $averageScore = $resultCount > 0
            ? round(
                $totalScore / $resultCount,
                2
            )
            : 0.0;

        return [
            'student' => $student,
            'academic_session' => $academicSession,
            'term' => $term,
            'results' => $reportResults,
            'total_score' => $totalScore,
            'average_score' => $averageScore,
            'result_count' => $resultCount,
        ];
    }
}