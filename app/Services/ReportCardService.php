<?php

declare(strict_types=1);

namespace SchoolERP\Services;

use RuntimeException;
use SchoolERP\Models\AcademicSession;
use SchoolERP\Models\Classroom;
use SchoolERP\Models\Student;
use SchoolERP\Models\Term;
use SchoolERP\Repositories\AcademicResultRepository;
use SchoolERP\Repositories\AcademicSessionRepository;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TermRepository;

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
        private TermRepository $terms,
        private ClassroomRepository $classrooms
    ) {
    }

    /**
     * Build a student's report card.
     *
     * @return array{
     *     student: Student,
     *     classroom: Classroom|null,
     *     academic_session: AcademicSession,
     *     term: Term,
     *     results: array<int,array<string,mixed>>,
     *     total_score: int,
     *     average_score: float,
     *     result_count: int,
     *     position: int|null,
     *     ranked_students: int
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
         * Load the student's classroom.
         */
        $classroom = null;

        if ($student->classroom_id !== null) {
            $classroom = $this->classrooms->find(
                (int) $student->classroom_id
            );

            if ($classroom !== null) {
                $student->setRelation(
                    'classroom',
                    $classroom
                );
            }
        }

        /*
         * Load all subjects so historical results remain
         * readable after a subject has been deactivated.
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

        /*
         * Get this student's results.
         */
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

            $hasTotalScore =
                $record['total_score'] !== null;

            $score = $hasTotalScore
                ? (int) $record['total_score']
                : 0;

            $reportResults[] = [
                'id' => (int) (
                    $record['id'] ?? 0
                ),

                'subject_id' => $subjectId,

                'subject_name' => (
                    $subjectLookup[$subjectId]['name']
                    ?? 'Unknown Subject'
                ),

                'subject_code' => (
                    $subjectLookup[$subjectId]['code']
                    ?? ''
                ),

                'ca_score' => (
                    $record['ca_score'] ?? null
                ),

                'exam_score' => (
                    $record['exam_score'] ?? null
                ),

                'total_score' => (
                    $record['total_score'] ?? null
                ),

                'grade' => (string) (
                    $record['grade'] ?? ''
                ),

                'remark' => (string) (
                    $record['remark'] ?? ''
                ),
            ];

            if ($hasTotalScore) {
                $totalScore += $score;
                $resultCount++;
            }
        }

        $averageScore = $resultCount > 0
            ? round(
                $totalScore / $resultCount,
                2
            )
            : 0.0;

        /*
         * Calculate class position.
         */
        $ranking = $this->calculateClassRanking(
            $student,
            $academicSessionId,
            $termId
        );

        return [
            'student' => $student,
            'classroom' => $classroom,
            'academic_session' => $academicSession,
            'term' => $term,
            'results' => $reportResults,
            'total_score' => $totalScore,
            'average_score' => $averageScore,
            'result_count' => $resultCount,
            'position' => $ranking['position'],
            'ranked_students' => $ranking['ranked_students'],
        ];
    }

    /**
     * Calculate a student's position within their classroom.
     *
     * Ranking uses competition ranking:
     *
     * 1st
     * 2nd
     * 2nd
     * 4th
     *
     * A student must have at least one recorded total score
     * to participate in the ranking.
     *
     * @return array{
     *     position: int|null,
     *     ranked_students: int
     * }
     */
    private function calculateClassRanking(
        Student $student,
        int $academicSessionId,
        int $termId
    ): array {
        if ($student->classroom_id === null) {
            return [
                'position' => null,
                'ranked_students' => 0,
            ];
        }

        /*
         * Get students in the same classroom.
         */
        $classroomStudents = $this->students->inClassroom(
            (int) $student->classroom_id
        );

        if ($classroomStudents === []) {
            return [
                'position' => null,
                'ranked_students' => 0,
            ];
        }

        /*
         * Build a lookup of students who belong to the class.
         */
        $studentIds = [];

        foreach ($classroomStudents as $classroomStudent) {
            $studentIds[(int) $classroomStudent['id']] = true;
        }

        /*
         * Get all results for this session and term.
         */
        $results = $this->results->forSessionAndTerm(
            $academicSessionId,
            $termId
        );

        /*
         * Accumulate total marks per student.
         */
        $totals = [];

        foreach ($results as $result) {
            $resultStudentId = (int) (
                $result['student_id'] ?? 0
            );

            /*
             * Ignore students outside this classroom.
             */
            if (!isset($studentIds[$resultStudentId])) {
                continue;
            }

            /*
             * Ignore results without a total score.
             */
            if ($result['total_score'] === null) {
                continue;
            }

            if (!isset($totals[$resultStudentId])) {
                $totals[$resultStudentId] = 0;
            }

            $score = (int) $result['total_score'];

            $totals[$resultStudentId] += $score;
        }

        /*
         * The student cannot be ranked without results.
         */
        $currentStudentId = (int) $student->id;

        if (!isset($totals[$currentStudentId])) {
            return [
                'position' => null,
                'ranked_students' => count($totals),
            ];
        }

        /*
         * Highest total score ranks first.
         */
        arsort(
            $totals,
            SORT_NUMERIC
        );

        $position = 0;
        $rank = 0;
        $previousScore = null;

        foreach ($totals as $rankedStudentId => $score) {
            $position++;

            /*
             * Assign a new rank only when the score changes.
             *
             * Example:
             *
             * 100 → 1
             * 90  → 2
             * 90  → 2
             * 80  → 4
             */
            if (
                $previousScore === null
                || $score < $previousScore
            ) {
                $rank = $position;
            }

            if (
                (int) $rankedStudentId === $currentStudentId
            ) {
                return [
                    'position' => $rank,
                    'ranked_students' => count($totals),
                ];
            }

            $previousScore = $score;
        }

        return [
            'position' => null,
            'ranked_students' => count($totals),
        ];
    }
}