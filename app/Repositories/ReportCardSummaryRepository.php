<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\ReportCardSummary;

final class ReportCardSummaryRepository extends Repository
{
    /**
     * Create a ReportCardSummaryRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new ReportCardSummary()
        );
    }

    /**
     * Find a summary by ID.
     */
    public function find(
        int $id
    ): ?ReportCardSummary {
        return $this->model->find($id);
    }

    /**
     * Find a student's report summary for a
     * particular session and term.
     */
    public function findForStudent(
        int $studentId,
        int $academicSessionId,
        int $termId
    ): ?ReportCardSummary {
        $record = $this->model
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
            ->first();

        if ($record === null) {
            return null;
        }

        return (new ReportCardSummary())->fill(
            $record
        );
    }

    /**
     * Create a report card summary.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create(
            $data
        );
    }

    /**
     * Update a report card summary.
     */
    public function updateSummary(
        int $id,
        array $data
    ): bool {
        $summary = $this->find(
            $id
        );

        if ($summary === null) {
            return false;
        }

        return $summary->update(
            $data
        ) > 0;
    }

    /**
     * Create or update a student's report summary.
     *
     * @return ReportCardSummary
     */
    public function saveForStudent(
        int $studentId,
        int $academicSessionId,
        int $termId,
        array $data
    ): ReportCardSummary {
        $summary = $this->findForStudent(
            $studentId,
            $academicSessionId,
            $termId
        );

        if ($summary === null) {
            $summary = $this->model->create(
                array_merge(
                    $data,
                    [
                        'student_id' =>
                            $studentId,

                        'academic_session_id' =>
                            $academicSessionId,

                        'term_id' =>
                            $termId,
                    ]
                )
            );

            /*
             * The framework's Model::create() does not
             * necessarily hydrate the auto-increment ID,
             * so retrieve the record through its unique key.
             */
            $summary = $this->findForStudent(
                $studentId,
                $academicSessionId,
                $termId
            );

            if ($summary === null) {
                throw new \RuntimeException(
                    'Unable to create report card summary.'
                );
            }

            return $summary;
        }

        $updated = $summary->update(
            $data
        );

        if (!$updated) {
            /*
             * Even when no database column changes,
             * the summary still exists and is valid.
             */
            return $summary;
        }

        return $this->findForStudent(
            $studentId,
            $academicSessionId,
            $termId
        ) ?? $summary;
    }
}
