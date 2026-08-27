<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\Term;

final class TermRepository extends Repository
{
    /**
     * Create a TermRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new Term()
        );
    }

    /**
     * Get all terms ordered by display order.
     *
     * @return array<int,array<string,mixed>>
     */
    public function allOrdered(): array
    {
        return $this->model
            ->query()
            ->orderBy(
                'sort_order',
                'ASC'
            )
            ->get();
    }

    /**
     * Get active terms.
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
                'sort_order',
                'ASC'
            )
            ->get();
    }

    /**
     * Find a term by ID.
     */
    public function find(
        int $id
    ): ?Term {
        return $this->model->find($id);
    }

    /**
     * Create a term.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create($data);
    }

    /**
     * Update a term.
     */
    public function updateTerm(
        int $id,
        array $data
    ): bool {
        $term = $this->find($id);

        if ($term === null) {
            return false;
        }

        return $term->update($data) > 0;
    }

    /**
     * Activate a term.
     */
    public function activate(
        int $id
    ): bool {
        return $this->updateTerm(
            $id,
            [
                'status' => 'active',
            ]
        );
    }

    /**
     * Deactivate a term.
     */
    public function deactivate(
        int $id
    ): bool {
        return $this->updateTerm(
            $id,
            [
                'status' => 'inactive',
            ]
        );
    }
}