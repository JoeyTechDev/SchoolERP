<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\AcademicResult;

final class AcademicResultRepository extends Repository
{
    /**
     * Create an AcademicResultRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new AcademicResult()
        );
    }

    /**
     * Find a result by ID.
     */
    public function find(
        int $id
    ): ?AcademicResult {
        return $this->model->find($id);
    }

    /**
     * Create a result.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create($data);
    }

    /**
     * Update a result.
     */
    public function updateResult(
        int $id,
        array $data
    ): bool {
        $result = $this->find($id);

        if ($result === null) {
            return false;
        }

        return $result->update($data) > 0;
    }

    /**
     * Delete a result.
     */
    public function delete(
        int $id
    ): bool {
        $result = $this->find($id);

        if ($result === null) {
            return false;
        }

        return $result->delete();
    }

    /**
     * Find a student's result for a specific subject,
     * academic session, and term.
     */
    public function findForStudent(
        int $studentId,
        int $subjectId,
        int $academicSessionId,
        int $termId
    ): ?AcademicResult {
        $record = $this->model
            ->query()
            ->where(
                'student_id',
                '=',
                $studentId
            )
            ->where(
                'subject_id',
                '=',
                $subjectId
            )
            ->where(
                'academic_session_id',
                '=',
                $academicSessionId
            )
            ->where(
                'term_id',
                '=',
                $termId
            )
            ->first();

        if ($record === null) {
            return null;
        }

        return (new AcademicResult())->fill(
            $record
        );
    }

    /**
     * Get all results for a student in a specific
     * academic session and term.
     *
     * @return array<int,array<string,mixed>>
     */
    public function forStudent(
        int $studentId,
        int $academicSessionId,
        int $termId
    ): array {
        return $this->model
            ->query()
            ->where(
                'student_id',
                '=',
                $studentId
            )
            ->where(
                'academic_session_id',
                '=',
                $academicSessionId
            )
            ->where(
                'term_id',
                '=',
                $termId
            )
            ->orderBy(
                'subject_id',
                'ASC'
            )
            ->get();
    }

    /**
     * Get all results for a subject in a session and term.
     *
     * @return array<int,array<string,mixed>>
     */
    public function forSubject(
        int $subjectId,
        int $academicSessionId,
        int $termId
    ): array {
        return $this->model
            ->query()
            ->where(
                'subject_id',
                '=',
                $subjectId
            )
            ->where(
                'academic_session_id',
                '=',
                $academicSessionId
            )
            ->where(
                'term_id',
                '=',
                $termId
            )
            ->orderBy(
                'student_id',
                'ASC'
            )
            ->get();
    }

/**
 * Get all academic results for a session and term.
 *
 * @return array<int,array<string,mixed>>
 */
public function forSessionAndTerm(
    int $academicSessionId,
    int $termId
): array {
    return $this->model
        ->query()
        ->where(
            'academic_session_id',
            '=',
            $academicSessionId
        )
        ->where(
            'term_id',
            '=',
            $termId
        )
        ->get();
}
}