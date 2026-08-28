<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Attendance;

final class AttendanceRepository extends Repository
{
    /**
     * Create an AttendanceRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new Attendance()
        );
    }

    /**
     * Find an attendance record by ID.
     */
    public function find(
        int $id
    ): ?Attendance {
        return $this->model->find($id);
    }

    /**
     * Create an attendance record.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create($data);
    }

    /**
     * Update an attendance record.
     */
    public function updateAttendance(
        int $id,
        array $data
    ): bool {
        $attendance = $this->find($id);

        if ($attendance === null) {
            return false;
        }

        return $attendance->update($data) > 0;
    }

    /**
     * Delete an attendance record.
     */
    public function delete(
        int $id
    ): bool {
        $attendance = $this->find($id);

        if ($attendance === null) {
            return false;
        }

        return $attendance->delete();
    }

    /**
     * Find a student's attendance record for a specific date,
     * academic session, and term.
     */
    public function findForStudentDate(
        int $studentId,
        string $attendanceDate,
        int $academicSessionId,
        int $termId
    ): ?Attendance {
        $record = $this->model
            ->query()
            ->where(
                'student_id',
                '=',
                $studentId
            )
            ->where(
                'attendance_date',
                '=',
                $attendanceDate
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

        return (new Attendance())->fill(
            $record
        );
    }

    /**
     * Get all attendance records for a specific date,
     * academic session, and term.
     *
     * @return array<int,array<string,mixed>>
     */
    public function forDate(
        string $attendanceDate,
        int $academicSessionId,
        int $termId
    ): array {
        return $this->model
            ->query()
            ->where(
                'attendance_date',
                '=',
                $attendanceDate
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
     * Get all attendance records for a student
     * within a session and term.
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
                'attendance_date',
                'DESC'
            )
            ->get();
    }

    /**
     * Get all attendance records for a session and term.
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
            ->orderBy(
                'attendance_date',
                'DESC'
            )
            ->orderBy(
                'student_id',
                'ASC'
            )
            ->get();
    }
}