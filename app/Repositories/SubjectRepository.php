<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Subject;

final class SubjectRepository extends Repository
{
    /**
     * Create a SubjectRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new Subject()
        );
    }

    /**
     * Get all active subjects ordered by name.
     *
     * @return array<int,array<string,mixed>>
     */
    public function active(): array
    {
        return $this->model
            ->query()
            ->where(
                'status',
                '=',
                'active'
            )
            ->orderBy(
                'name',
                'ASC'
            )
            ->get();
    }

    /**
     * Get all subjects ordered by name.
     *
     * @return array<int,array<string,mixed>>
     */
    public function allOrdered(): array
    {
        return $this->model
            ->query()
            ->orderBy(
                'name',
                'ASC'
            )
            ->get();
    }

    /**
     * Find a subject by ID.
     */
    public function find(
        int $id
    ): ?Subject {
        return $this->model->find($id);
    }

    /**
     * Create a subject.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create($data);
    }

    /**
     * Update a subject.
     */
    public function updateSubject(
        int $id,
        array $data
    ): bool {
        $subject = $this->find($id);

        if ($subject === null) {
            return false;
        }

        return $subject->update($data) > 0;
    }

    /**
     * Rename a subject.
     */
    public function rename(
        int $id,
        string $name
    ): bool {
        return $this->updateSubject(
            $id,
            [
                'name' => trim($name),
            ]
        );
    }

    /**
     * Deactivate a subject.
     */
    public function deactivate(
        int $id
    ): bool {
        return $this->updateSubject(
            $id,
            [
                'status' => 'inactive',
            ]
        );
    }

    /**
     * Activate a subject.
     */
    public function activate(
        int $id
    ): bool {
        return $this->updateSubject(
            $id,
            [
                'status' => 'active',
            ]
        );
    }

    /**
     * Delete a subject.
     *
     * This should only be used when the subject has
     * no academic records depending on it.
     */
    public function delete(
        int $id
    ): bool {
        $subject = $this->find($id);

        if ($subject === null) {
            return false;
        }

        return $subject->delete();
    }
}