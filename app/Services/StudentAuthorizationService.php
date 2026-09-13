<?php

declare(strict_types=1);

namespace SchoolERP\Services;

use SchoolERP\Models\Student;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Session\SessionInterface;

final class StudentAuthorizationService
{
    /**
     * Student role.
     */
    private const ROLE_STUDENT = 3;

    /**
     * Constructor.
     */
    public function __construct(
        private SessionInterface $session,
        private StudentRepository $students
    ) {
    }

    /**
     * Determine whether the current user is a Student.
     */
    public function isStudent(): bool
    {
        return (int) $this->session->get(
            'role_id',
            0
        ) === self::ROLE_STUDENT;
    }

    /**
     * Get the current authenticated user ID.
     */
    public function currentUserId(): int
    {
        return (int) $this->session->get(
            'user_id',
            0
        );
    }

    /**
     * Get the Student profile linked to the
     * currently authenticated user.
     */
    public function currentStudent(): ?Student
    {
        if (!$this->isStudent()) {
            return null;
        }

        $userId = $this->currentUserId();

        if ($userId <= 0) {
            return null;
        }

        return $this->students->findByUserId(
            $userId
        );
    }

    /**
     * Get the current Student's database ID.
     */
    public function currentStudentId(): ?int
    {
        $student = $this->currentStudent();

        if ($student === null) {
            return null;
        }

        $studentId = (int) (
            $student->id ?? 0
        );

        return $studentId > 0
            ? $studentId
            : null;
    }

    /**
     * Determine whether the current user can access
     * a particular Student record.
     *
     * A Student can access only their own record.
     */
    public function canAccessStudent(
        int $studentId
    ): bool {
        if (
            !$this->isStudent()
            || $studentId <= 0
        ) {
            return false;
        }

        $currentStudentId =
            $this->currentStudentId();

        if ($currentStudentId === null) {
            return false;
        }

        return $currentStudentId === $studentId;
    }

    /**
     * Require a valid Student profile for the
     * authenticated account.
     *
     * Returns null when valid; otherwise returns
     * an explanatory message.
     */
    public function authorizationError(): ?string
    {
        if (!$this->isStudent()) {
            return '403 Forbidden - Student access is required.';
        }

        if ($this->currentUserId() <= 0) {
            return '403 Forbidden - No authenticated student account was found.';
        }

        if ($this->currentStudent() === null) {
            return '403 Forbidden - No student profile is linked to this account.';
        }

        return null;
    }
}
