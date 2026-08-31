<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Student;
use SchoolERP\Query\Pagination\Paginator;

final class StudentRepository extends Repository
{
    /**
     * Constructor.
     */
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
            ->scope(
                'inClassroom',
                $classroomId
            )
            ->query()
            ->get();
    }

    /**
     * Get all students ordered by last name
     * and first name.
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
     * Paginate students.
     */
    public function paginate(
        int $page = 1,
        int $perPage = 10
    ): Paginator {
        $pagination = $this->model
            ->query()
            ->paginate(
                $perPage,
                $page
            );

        return $this->attachClassroomNames(
            $pagination
        );
    }

    /**
     * Search students by admission number,
     * first name, or last name.
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

            /*
             * The search should match any of:
             *
             * admission_number
             * first_name
             * last_name
             */
            $query
                ->whereLike(
                    'admission_number',
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
                );
        }

        $pagination = $query->paginate(
            $perPage,
            $page
        );

        return $this->attachClassroomNames(
            $pagination
        );
    }

    /**
     * Attach classroom names to paginated students.
     */
    private function attachClassroomNames(
        Paginator $pagination
    ): Paginator {
        $items = $pagination->items();

        foreach ($items as &$student) {

            $classroomId =
                $student['classroom_id']
                ?? null;

            if ($classroomId === null) {

                $student['classroom_name'] =
                    null;

                continue;
            }

            $model = $this->model->find(
                (int) $student['id']
            );

            $classroom = $model !== null
                ? $model->classroom()->get()
                : null;

            $student['classroom_name'] =
                $classroom !== null
                    ? $classroom->name
                    : null;
        }

        unset($student);

        return new Paginator(
            $items,
            $pagination->total(),
            $pagination->perPage(),
            $pagination->currentPage()
        );
    }
}
