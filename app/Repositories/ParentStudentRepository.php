<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Database\Database;

final class ParentStudentRepository
{
    public function __construct(
        private Database $database
    ) {
    }

    /**
     * Get students linked to a parent user account.
     *
     * @return array<int, array<string, mixed>>
     */
    public function studentsForParent(
        int $parentUserId
    ): array {
        if ($parentUserId <= 0) {
            return [];
        }

        return $this->database->select(
            'SELECT
                s.id,
                s.admission_number,
                s.first_name,
                s.last_name,
                s.gender,
                s.classroom_id,
                c.name AS classroom_name
             FROM parent_student AS ps
             INNER JOIN students AS s
                ON s.id = ps.student_id
             LEFT JOIN classrooms AS c
                ON c.id = s.classroom_id
             WHERE ps.parent_user_id = :parent_user_id
             ORDER BY s.last_name ASC, s.first_name ASC',
            [
                'parent_user_id' => $parentUserId,
            ]
        );
    }

    /**
     * Check whether a student is linked to a parent.
     */
    public function hasStudent(
        int $parentUserId,
        int $studentId
    ): bool {
        if (
            $parentUserId <= 0
            || $studentId <= 0
        ) {
            return false;
        }

        $record = $this->database->selectOne(
            'SELECT 1
             FROM parent_student
             WHERE parent_user_id = :parent_user_id
               AND student_id = :student_id
             LIMIT 1',
            [
                'parent_user_id' => $parentUserId,
                'student_id' => $studentId,
            ]
        );

        return $record !== null;
    }

    /**
     * Retrieve one student's details for a specific parent.
     *
     * The SQL relationship check prevents this method from
     * returning details for an unlinked student.
     *
     * @return array<string, mixed>|null
     */
    public function studentForParent(
        int $parentUserId,
        int $studentId
    ): ?array {
        if (
            $parentUserId <= 0
            || $studentId <= 0
        ) {
            return null;
        }

        return $this->database->selectOne(
            'SELECT
                s.id,
                s.admission_number,
                s.first_name,
                s.last_name,
                s.date_of_birth,
                s.gender,
                s.classroom_id,
                c.name AS classroom_name
             FROM parent_student AS ps
             INNER JOIN students AS s
                ON s.id = ps.student_id
             LEFT JOIN classrooms AS c
                ON c.id = s.classroom_id
             WHERE ps.parent_user_id = :parent_user_id
               AND ps.student_id = :student_id
             LIMIT 1',
            [
                'parent_user_id' => $parentUserId,
                'student_id' => $studentId,
            ]
        );
    }
}
