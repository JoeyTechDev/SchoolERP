<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Student;

final class StudentRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(
            new Student()
        );
    }

    /**
     * Get students belonging to a classroom.
     *
     * @return array<int,array<string,mixed>>
     */
    public function inClassroom(int $classroomId): array
    {
        return $this->model
            ->scope('inClassroom', $classroomId)
            ->query()
            ->get();
    }

    /**
     * Paginate students.
     */
    public function paginate(
        int $page = 1,
        int $perPage = 10
    ) {
        return $this->model
            ->query()
            ->paginate($page, $perPage);
    }
}