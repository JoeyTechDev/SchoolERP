<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Teacher;
use SchoolERP\Query\Pagination\Paginator;

final class TeacherRepository extends Repository
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(
            new Teacher()
        );
    }

    /**
     * Get all teachers ordered by last name and first name.
     *
     * @return array<int,array<string,mixed>>
     */
    public function allOrdered(): array
    {
        return $this->model
            ->query()
            ->orderBy(
                'last_name',
                'ASC'
            )
            ->orderBy(
                'first_name',
                'ASC'
            )
            ->get();
    }

    /**
     * Get paginated teachers.
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
     * Search teachers by employee number,
     * first name, last name, or email.
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
                ->whereLike(
                    'employee_number',
                    $pattern
                )
                ->orWhere(
                    'first_name',
                    'LIKE',
                    $pattern
                )
                ->orWhere(
                    'last_name',
                    'LIKE',
                    $pattern
                )
                ->orWhere(
                    'email',
                    'LIKE',
                    $pattern
                );
        }

        return $query->paginate(
            $perPage,
            $page
        );
    }

    /**
     * Find a teacher by employee number.
     */
    public function findByEmployeeNumber(
        string $employeeNumber
    ): ?Teacher {
        $record = $this->model
            ->query()
            ->where(
                'employee_number',
                '=',
                $employeeNumber
            )
            ->first();

        if ($record === null) {
            return null;
        }

        return (new Teacher())->fill(
            $record
        );
    }

    /**
     * Create a teacher.
     */
    public function create(
        array $data
    ): int {
        return $this->model->create(
            $data
        );
    }
}
