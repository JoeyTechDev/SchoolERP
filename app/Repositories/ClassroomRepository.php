<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Classroom;

final class ClassroomRepository extends Repository
{
    /**
     * Create a ClassroomRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new Classroom()
        );
    }

    /**
     * Get all classrooms ordered by name,
     * including the number of assigned students.
     *
     * @return array<int,array<string,mixed>>
     */
    public function allOrdered(): array
    {
        $classrooms = $this->model
            ->query()
            ->orderBy('name', 'ASC')
            ->get();

        foreach ($classrooms as &$classroom) {
            $classroomModel = $this->model->find(
                (int) $classroom['id']
            );

            $classroom['student_count'] = $classroomModel === null
                ? 0
                : count(
                    $classroomModel
                        ->students()
                        ->get()
                );
        }

        unset($classroom);

        return $classrooms;
    }

    /**
     * Find a classroom by ID.
     */
    public function find(
        int $id
    ): ?Classroom {
        return $this->model->find($id);
    }

    /**
     * Create a classroom.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create($data);
    }

    /**
     * Update a classroom name.
     */
    public function rename(
        int $id,
        string $name
    ): bool {
        $classroom = $this->find($id);

        if ($classroom === null) {
            return false;
        }

        return $classroom->update([
            'name' => trim($name),
        ]) > 0;
    }

    /**
     * Determine whether a classroom has students.
     */
    public function hasStudents(
        int $id
    ): bool {
        $classroom = $this->find($id);

        if ($classroom === null) {
            return false;
        }

        return count(
            $classroom
                ->students()
                ->get()
        ) > 0;
    }

    /**
     * Delete a classroom.
     *
     * Refuses to delete a classroom that still has students.
     */
    public function delete(
        int $id
    ): bool {
        $classroom = $this->find($id);

        if ($classroom === null) {
            return false;
        }

        if ($this->hasStudents($id)) {
            return false;
        }

        return $classroom->delete();
    }
}

