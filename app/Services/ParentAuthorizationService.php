<?php

declare(strict_types=1);

namespace SchoolERP\Services;

use SchoolERP\Repositories\ParentStudentRepository;
use SchoolERP\Session\SessionInterface;

final class ParentAuthorizationService
{
    private const ROLE_PARENT = 4;

    public function __construct(
        private SessionInterface $session,
        private ParentStudentRepository $parentStudents
    ) {
    }

    /**
     * Determine whether the authenticated account is a parent.
     */
    public function isParent(): bool
    {
        return (int) $this->session->get(
            'role_id',
            0
        ) === self::ROLE_PARENT;
    }

    /**
     * Get the authenticated parent's user ID.
     */
    public function currentUserId(): int
    {
        return (int) $this->session->get(
            'user_id',
            0
        );
    }

    /**
     * Get children linked to the authenticated parent.
     *
     * Never accept the parent ID from a request parameter.
     *
     * @return array<int, array<string, mixed>>
     */
    public function currentStudents(): array
    {
        if (!$this->isParent()) {
            return [];
        }

        $parentUserId = $this->currentUserId();

        if ($parentUserId <= 0) {
            return [];
        }

        return $this->parentStudents->studentsForParent(
            $parentUserId
        );
    }

    /**
     * Determine whether the authenticated parent can
     * access a specific student.
     */
    public function canAccessStudent(
        int $studentId
    ): bool {
        if (
            !$this->isParent()
            || $studentId <= 0
        ) {
            return false;
        }

        $parentUserId = $this->currentUserId();

        if ($parentUserId <= 0) {
            return false;
        }

        return $this->parentStudents->hasStudent(
            $parentUserId,
            $studentId
        );
    }

    /**
     * Return an authorization error, or null when valid.
     */
    public function authorizationError(): ?string
    {
        if (!$this->isParent()) {
            return '403 Forbidden - Parent access is required.';
        }

        if ($this->currentUserId() <= 0) {
            return '403 Forbidden - No authenticated parent account was found.';
        }

        return null;
    }
}
