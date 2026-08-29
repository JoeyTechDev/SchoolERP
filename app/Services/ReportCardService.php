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
use SchoolERP\Repositories\AttendanceRepository;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Repositories\ReportCardSummaryRepository;
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
        private ClassroomRepository $classrooms,
        private AttendanceRepository $attendance,
        private ReportCardSummaryRepository $summaries
    ) {
    }

    /**
     * Build a complete student report card.
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
     *     ranked_students: int,
     *     attendance_summary: array{
     *         total_days: int,
     *         present: int,
     *         absent: int,
     *         late: int,
     *         excused: int,
     *         attendance_rate: float
     *     },
     *     report_summary: \SchoolERP\Models\ReportCardSummary|null
     * }
     */
    public function build(
        int $studentId,
        int $academicSessionId,
        int $termId
    ): array {
        /*
         * --------------------------------------------------------------
         * Student
         * --------------------------------------------------------------
         */
        $student = $this->students->find(
            $studentId
        );

        if ($student === null) {
            throw new RuntimeException(
                'Student not found.'
            );
        }

        /*
         * --------------------------------------------------------------
         * Academic session
         * --------------------------------------------------------------
         */
        $academicSession = $this->sessions->find(
            $academicSessionId
        );

        if ($academicSession === null) {
            throw new RuntimeException(
                'Academic session not found.'
            );
        }

        /*
         * --------------------------------------------------------------
         * Term
         * --------------------------------------------------------------
         */
        $term = $this->terms->find(
            $termId
        );

        if ($term === null) {
            throw new RuntimeException(
                'Academic term not found.'
            );
        }

        /*
         * --------------------------------------------------------------
         * Classroom
         * --------------------------------------------------------------
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
         * --------------------------------------------------------------
         * Subjects
         * --------------------------------------------------------------
         *
         * Load all subjects so historical results remain readable even
         * when a subject has subsequently been deactivated.
         */
        $subjects = $this->subjects->allOrdered();

        $subjectLookup = [];

        foreach ($subjects as $subject) {
            $subjectId = (int) (
                $subject['id'] ?? 0
            );

            $subjectLookup[$subjectId] = [
                'name' => (string) (
                    $subject['name'] ?? ''
                ),

                'code' => (string) (
                    $subject['code'] ?? ''
                ),
            ];
        }

        /*
         * --------------------------------------------------------------
         * Academic results
         * --------------------------------------------------------------
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
         * --------------------------------------------------------------
         * Class ranking
         * --------------------------------------------------------------
         */
        $ranking = $this->calculateClassRanking(
            $student,
            $academicSessionId,
            $termId
        );

        /*
         * --------------------------------------------------------------
         * Attendance summary
         * --------------------------------------------------------------
         */
        $attendanceSummary =
            $this->attendance->summaryForStudent(
                $studentId,
                $academicSessionId,
                $termId
            );

        /*
         * --------------------------------------------------------------
         * Report-card remarks and promotion
         * --------------------------------------------------------------
         */
        $reportSummary =
            $this->summaries->findForStudent(
                $studentId,
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

            'ranked_students' =>
                $ranking['ranked_students'],

            'attendance_summary' =>
                $attendanceSummary,

            'report_summary' =>
                $reportSummary,
        ];
    }

    /**
     * Calculate class position.
     *
     * Uses competition ranking:
     *
     * 1st
     * 2nd
     * 2nd
     * 4th
     *
     * Students are ranked only against students in the same classroom.
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

        $classroomStudents =
            $this->students->inClassroom(
                (int) $student->classroom_id
            );

        if ($classroomStudents === []) {
            return [
                'position' => null,
                'ranked_students' => 0,
            ];
        }

        /*
         * Build classroom student lookup.
         */
        $studentIds = [];

        foreach ($classroomStudents as $classroomStudent) {
            $studentIds[
                (int) $classroomStudent['id']
            ] = true;
        }

        /*
         * Get all results for the selected session and term.
         */
        $results = $this->results->forSessionAndTerm(
            $academicSessionId,
            $termId
        );

        /*
         * Accumulate totals per student.
         */
        $totals = [];

        foreach ($results as $result) {
            $resultStudentId = (int) (
                $result['student_id'] ?? 0
            );

            /*
             * Ignore students outside the classroom.
             */
            if (!isset(
                $studentIds[$resultStudentId]
            )) {
                continue;
            }

            /*
             * Ignore incomplete results.
             */
            if ($result['total_score'] === null) {
                continue;
            }

            if (!isset(
                $totals[$resultStudentId]
            )) {
                $totals[$resultStudentId] = 0;
            }

            $totals[$resultStudentId] +=
                (int) $result['total_score'];
        }

        $currentStudentId = (int) $student->id;

        /*
         * A student without results cannot be ranked.
         */
        if (!isset(
            $totals[$currentStudentId]
        )) {
            return [
                'position' => null,
                'ranked_students' => count($totals),
            ];
        }

        /*
         * Highest total comes first.
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
             * Competition ranking.
             */
            if (
                $previousScore === null
                || $score < $previousScore
            ) {
                $rank = $position;
            }

            if (
                (int) $rankedStudentId
                === $currentStudentId
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