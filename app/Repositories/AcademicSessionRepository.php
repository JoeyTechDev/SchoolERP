<?php

declare(strict_types=1);

namespace SchoolERP\Repositories;

use SchoolERP\Models\AcademicSession;

final class AcademicSessionRepository extends Repository
{
    /**
     * Create an AcademicSessionRepository.
     */
    public function __construct()
    {
        parent::__construct(
            new AcademicSession()
        );
    }

    /**
     * Get all academic sessions.
     *
     * @return array<int,array<string,mixed>>
     */
    public function allOrdered(): array
    {
        return $this->model
            ->query()
            ->orderBy('name', 'DESC')
            ->get();
    }

    /**
     * Get all active academic sessions.
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
                'DESC'
            )
            ->get();
    }

    /**
     * Get the current academic session.
     */
    public function current(): ?AcademicSession
    {
        $record = $this->model
            ->query()
            ->where(
                'is_current',
                '=',
                1
            )
            ->where(
                'status',
                '=',
                'active'
            )
            ->first();

        if ($record === null) {
            return null;
        }

        return (new AcademicSession())->fill(
            $record
        );
    }

    /**
     * Find an academic session by ID.
     */
    public function find(
        int $id
    ): ?AcademicSession {
        return $this->model->find($id);
    }

    /**
     * Create an academic session.
     */
    public function create(
        array $data
    ): mixed {
        return $this->model->create($data);
    }

    /**
     * Update an academic session.
     */
    public function updateSession(
        int $id,
        array $data
    ): bool {
        $session = $this->find($id);

        if ($session === null) {
            return false;
        }

        return $session->update($data) > 0;
    }

    /**
     * Set one academic session as the current session.
     *
     * All other sessions are automatically unset as current.
     */
    public function setCurrent(
        int $id
    ): bool {
        $session = $this->find($id);

        if ($session === null) {
            return false;
        }

        /*
         * Remove the current flag from all sessions.
         */
        $this->model
            ->query()
            ->update([
                'is_current' => 0,
            ]);

        /*
         * Set the selected session as current.
         */
        return $session->update([
            'is_current' => 1,
            'status' => 'active',
        ]) > 0;
    }

    /**
     * Activate an academic session.
     */
    public function activate(
        int $id
    ): bool {
        return $this->updateSession(
            $id,
            [
                'status' => 'active',
            ]
        );
    }

    /**
     * Deactivate an academic session.
     */
    public function deactivate(
        int $id
    ): bool {
        $session = $this->find($id);

        if ($session === null) {
            return false;
        }

        /*
         * The current session should never be deactivated.
         */
        if ((int) $session->is_current === 1) {
            return false;
        }

        return $session->update([
            'status' => 'inactive',
        ]) > 0;
    }
}