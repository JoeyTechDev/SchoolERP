<?php

declare(strict_types=1);

namespace SchoolERP\Services;

use SchoolERP\Models\Teacher;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\TeacherAssignmentRepository;
use SchoolERP\Repositories\TeacherRepository;
use SchoolERP\Session\SessionInterface;

final class TeacherAuthorizationService
{
    /**
     * Administrator role.
     */
    private const ROLE_ADMIN = 1;

    /**
     * Teacher role.
     */
    private const ROLE_TEACHER = 2;

    /**
     * Constructor.
     */
    public function __construct(
        private SessionInterface $session,
        private TeacherRepository $teachers,
        private TeacherAssignmentRepository $assignments,
        private StudentRepository $students
    ) {
    }

    /**
     * Determine whether current user is an administrator.
     */
    public function isAdmin(): bool
    {
        return (int) $this->session->get(
            'role_id',
            0
        ) === self::ROLE_ADMIN;
    }

    /**
     * Determine whether current user is a teacher.
     */
    public function isTeacher(): bool
    {
        return (int) $this->session->get(
            'role_id',
            0
        ) === self::ROLE_TEACHER;
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
     * Get the teacher profile linked to the current user.
     */
    public function currentTeacher(): ?Teacher
    {
        $userId = $this->currentUserId();

        if ($userId <= 0) {
            return null;
        }

        return $this->teachers->findByUserId(
            $userId
        );
    }

    /**
     * Get the current teacher's ID.
     */
    public function currentTeacherId(): ?int
    {
        $teacher = $this->currentTeacher();

        if ($teacher === null) {
            return null;
        }

        $teacherId = (int) (
            $teacher->id ?? 0
        );

        return $teacherId > 0
            ? $teacherId
            : null;
    }

    /**
     * Determine whether the current user can access a classroom.
     *
     * Administrators can access every classroom.
     */
    public function canAccessClassroom(
        int $classroomId
    ): bool {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isTeacher()) {
            return false;
        }

        if ($classroomId <= 0) {
            return false;
        }

        $teacherId = $this->currentTeacherId();

        if ($teacherId === null) {
            return false;
        }

        foreach (
            $this->assignments->forTeacher(
                $teacherId,
                true
            ) as $assignment
        ) {
            if (
                (int) (
                    $assignment->classroom_id
                    ?? 0
                ) === $classroomId
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the current user can manage a subject
     * in a classroom.
     *
     * Administrators can manage every subject/classroom pair.
     */
    public function canManageSubject(
        int $classroomId,
        int $subjectId
    ): bool {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isTeacher()) {
            return false;
        }

        if (
            $classroomId <= 0
            || $subjectId <= 0
        ) {
            return false;
        }

        $teacherId = $this->currentTeacherId();

        if ($teacherId === null) {
            return false;
        }

        $assignment =
            $this->assignments->findExact(
                $teacherId,
                $classroomId,
                $subjectId
            );

        if ($assignment === null) {
            return false;
        }

        return (int) (
            $assignment->is_active ?? 0
        ) === 1;
    }

    /**
     * Determine whether the current user can manage
     * a particular student.
     *
     * Teacher access is based on the student's classroom.
     */
    public function canManageStudent(
        int $studentId
    ): bool {
        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isTeacher()) {
            return false;
        }

        if ($studentId <= 0) {
            return false;
        }

        $student = $this->students->find(
            $studentId
        );

        if ($student === null) {
            return false;
        }

        $classroomId = (int) (
            $student->classroom_id ?? 0
        );

        return $this->canAccessClassroom(
            $classroomId
        );
    }
}
