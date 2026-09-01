<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\TeacherAssignment;

final class TeacherAssignmentRepository extends Repository
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(
            new TeacherAssignment()
        );
    }

    /**
     * Find an assignment by ID.
     */
    public function find(
        int $id
    ): ?TeacherAssignment {
        return $this->model->find(
            $id
        );
    }

    /**
     * Create an assignment.
     */
    public function create(
        array $data
    ): int {
        return $this->model->create(
            $data
        );
    }

    /**
     * Update an assignment.
     */
    public function updateAssignment(
        int $id,
        array $data
    ): bool {
        $assignment = $this->find(
            $id
        );

        if ($assignment === null) {
            return false;
        }

        return $assignment->update(
            $data
        ) > 0;
    }

    /**
     * Delete an assignment.
     */
    public function delete(
        int $id
    ): bool {
        $assignment = $this->find(
            $id
        );

        if ($assignment === null) {
            return false;
        }

        return $assignment->delete();
    }

    /**
     * Find an exact teacher/classroom/subject assignment.
     */
    public function findExact(
        int $teacherId,
        int $classroomId,
        int $subjectId
    ): ?TeacherAssignment {
        $record = $this->model
            ->query()
            ->where(
                'teacher_id',
                '=',
                $teacherId
            )
            ->where(
                'classroom_id',
                '=',
                $classroomId
            )
            ->where(
                'subject_id',
                '=',
                $subjectId
            )
            ->first();

        if ($record === null) {
            return null;
        }

        return (new TeacherAssignment())->fill(
            $record
        );
    }

    /**
     * Get all assignments for a teacher.
     *
     * @return array<int,TeacherAssignment>
     */
    public function forTeacher(
        int $teacherId,
        bool $activeOnly = true
    ): array {
        $query = $this->model
            ->query()
            ->where(
                'teacher_id',
                '=',
                $teacherId
            );

        if ($activeOnly) {
            $query->where(
                'is_active',
                '=',
                1
            );
        }

        return array_map(
            static function (
                array $record
            ): TeacherAssignment {
                return (new TeacherAssignment())
                    ->fill($record);
            },
            $query->get()
        );
    }

    /**
     * Get all assignments for a classroom.
     *
     * @return array<int,TeacherAssignment>
     */
    public function forClassroom(
        int $classroomId,
        bool $activeOnly = true
    ): array {
        $query = $this->model
            ->query()
            ->where(
                'classroom_id',
                '=',
                $classroomId
            );

        if ($activeOnly) {
            $query->where(
                'is_active',
                '=',
                1
            );
        }

        return array_map(
            static function (
                array $record
            ): TeacherAssignment {
                return (new TeacherAssignment())
                    ->fill($record);
            },
            $query->get()
        );
    }

    /**
     * Get all assignments for a subject.
     *
     * @return array<int,TeacherAssignment>
     */
    public function forSubject(
        int $subjectId,
        bool $activeOnly = true
    ): array {
        $query = $this->model
            ->query()
            ->where(
                'subject_id',
                '=',
                $subjectId
            );

        if ($activeOnly) {
            $query->where(
                'is_active',
                '=',
                1
            );
        }

        return array_map(
            static function (
                array $record
            ): TeacherAssignment {
                return (new TeacherAssignment())
                    ->fill($record);
            },
            $query->get()
        );
    }
}
