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

/**
 * Get attendance records for a date indexed by student ID.
 *
 * @return array<int,array<string,mixed>>
 */
public function forDateIndexedByStudent(
    string $attendanceDate,
    int $academicSessionId,
    int $termId
): array {
    $records = $this->forDate(
        $attendanceDate,
        $academicSessionId,
        $termId
    );

    $indexed = [];

    foreach ($records as $record) {
        $studentId = (int) (
            $record['student_id'] ?? 0
        );

        $indexed[$studentId] = $record;
    }

    return $indexed;
}

/**
 * Get attendance summary for a student in a session and term.
 *
 * Attendance rate is calculated as:
 *
 * (Present + Late) / Total Recorded Days * 100
 *
 * @return array{
 *     total_days: int,
 *     present: int,
 *     absent: int,
 *     late: int,
 *     excused: int,
 *     attendance_rate: float
 * }
 */
public function summaryForStudent(
    int $studentId,
    int $academicSessionId,
    int $termId
): array {
    $records = $this->forStudent(
        $studentId,
        $academicSessionId,
        $termId
    );

    $summary = [
        'total_days' => 0,
        'present' => 0,
        'absent' => 0,
        'late' => 0,
        'excused' => 0,
        'attendance_rate' => 0.0,
    ];

    foreach ($records as $record) {
        $summary['total_days']++;

        $status = strtolower(
            trim(
                (string) (
                    $record['status'] ?? ''
                )
            )
        );

        switch ($status) {
            case 'present':
                $summary['present']++;
                break;

            case 'absent':
                $summary['absent']++;
                break;

            case 'late':
                $summary['late']++;
                break;

            case 'excused':
                $summary['excused']++;
                break;
        }
    }

    if ($summary['total_days'] > 0) {
        $attendedDays =
            $summary['present']
            + $summary['late'];

        $summary['attendance_rate'] = round(
            (
                $attendedDays
                / $summary['total_days']
            ) * 100,
            2
        );
    }

    return $summary;
}
}