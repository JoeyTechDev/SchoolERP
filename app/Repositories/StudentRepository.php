<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Student;
use SchoolERP\Query\Pagination\Paginator;

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
    public function inClassroom(
        int $classroomId
    ): array {
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
    ): Paginator {
        return $this->model
            ->query()
            ->paginate(
                $perPage,
                $page
            );
    }

    /**
     * Search students by first name or last name
     * and return paginated results.
     */
    public function searchPaginated(
        string $search,
        int $page = 1,
        int $perPage = 10
    ): Paginator {
        $search = trim($search);

        $query = $this->model->query();

        if ($search !== '') {
            $pattern = '%' . $search . '%';

            $query
                ->whereLike('first_name', $pattern)
                ->orWhere('last_name', 'LIKE', $pattern);
        }

        return $query->paginate(
            $perPage,
            $page
        );
    }
}

