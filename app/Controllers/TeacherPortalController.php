<?php

declare(strict_types=1);

namespace SchoolERP\Controllers;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Repositories\ClassroomRepository;
use SchoolERP\Repositories\StudentRepository;
use SchoolERP\Repositories\SubjectRepository;
use SchoolERP\Repositories\TeacherAssignmentRepository;
use SchoolERP\Services\TeacherAuthorizationService;
use SchoolERP\Session\SessionInterface;
use SchoolERP\View\ViewFactory;

final class TeacherPortalController extends Controller
{
    /**
     * Teacher assignment repository.
     */
    private TeacherAssignmentRepository $assignments;

    /**
     * Classroom repository.
     */
    private ClassroomRepository $classrooms;

    /**
     * Subject repository.
     */
    private SubjectRepository $subjects;

    /**
     * Student repository.
     */
    private StudentRepository $students;

    /**
     * Teacher authorization service.
     */
    private TeacherAuthorizationService $authorization;

    /**
     * Constructor.
     */
    public function __construct(
        ViewFactory $views,
        SessionInterface $session,
        TeacherAssignmentRepository $assignments,
        ClassroomRepository $classrooms,
        SubjectRepository $subjects,
        StudentRepository $students,
        TeacherAuthorizationService $authorization
    ) {
        parent::__construct(
            $views,
            $session
        );

        $this->assignments = $assignments;
        $this->classrooms = $classrooms;
        $this->subjects = $subjects;
        $this->students = $students;
        $this->authorization = $authorization;
    }

    /**
     * Display the teacher dashboard.
     */
    public function dashboard(
        Request $request
    ): Response {
        $forbidden = $this->requireRole([2]);

        if ($forbidden !== null) {
            return $forbidden;
        }

        $teacher =
            $this->authorization->currentTeacher();

        if ($teacher === null) {
            return Response::make(
                '403 Forbidden - No teacher profile is linked to this account.',
                403
            );
        }

        $teacherId = (int) (
            $teacher->id ?? 0
        );

        if ($teacherId <= 0) {
            return Response::make(
                '403 Forbidden - Invalid teacher profile.',
                403
            );
        }

        /*
         * Only active teaching assignments are used
         * on the portal dashboard.
         */
        $assignments =
            $this->assignments->forTeacher(
                $teacherId,
                true
            );

        /*
         * Build classroom and subject lookup tables.
         */
        $classroomRecords =
            $this->classrooms->allOrdered();

        $subjectRecords =
            $this->subjects->allOrdered();

        $students =
            $this->students->allOrdered();

        $classroomLookup = [];

        foreach (
            $classroomRecords
            as $classroom
        ) {
            $classroomId = (int) (
                $classroom['id'] ?? 0
            );

            if ($classroomId <= 0) {
                continue;
            }

            $classroomLookup[$classroomId] =
                (string) (
                    $classroom['name'] ?? ''
                );
        }

        $subjectLookup = [];

        foreach (
            $subjectRecords
            as $subject
        ) {
            $subjectId = (int) (
                $subject['id'] ?? 0
            );

            if ($subjectId <= 0) {
                continue;
            }

            $subjectLookup[$subjectId] = [
                'name' => (string) (
                    $subject['name'] ?? ''
                ),
                'code' => (string) (
                    $subject['code'] ?? ''
                ),
            ];
        }

        /*
         * Count students by classroom.
         */
        $studentCounts = [];

        foreach (
            $students
            as $student
        ) {
            $classroomId = (int) (
                $student['classroom_id']
                ?? 0
            );

            if ($classroomId <= 0) {
                continue;
            }

            if (
                !isset(
                    $studentCounts[$classroomId]
                )
            ) {
                $studentCounts[$classroomId] = 0;
            }

            $studentCounts[$classroomId]++;
        }

        /*
         * Build dashboard classroom cards.
         *
         * One classroom may contain several subject
         * assignments for the same teacher.
         */
        $classroomCards = [];

        foreach (
            $assignments
            as $assignment
        ) {
            $classroomId = (int) (
                $assignment->classroom_id
                ?? 0
            );

            $subjectId = (int) (
                $assignment->subject_id
                ?? 0
            );

            if (
                $classroomId <= 0
                || $subjectId <= 0
            ) {
                continue;
            }

            if (
                !isset(
                    $classroomCards[$classroomId]
                )
            ) {
                $classroomCards[$classroomId] = [
                    'id' => $classroomId,

                    'name' =>
                        $classroomLookup[
                            $classroomId
                        ]
                        ?? 'Classroom #'
                            . $classroomId,

                    'student_count' =>
                        $studentCounts[
                            $classroomId
                        ]
                        ?? 0,

                    'subjects' => [],
                ];
            }

            if (
                isset(
                    $subjectLookup[$subjectId]
                )
            ) {
                $classroomCards[
                    $classroomId
                ]['subjects'][$subjectId] =
                    $subjectLookup[$subjectId];
            }
        }

        /*
         * Re-index classroom cards and subjects.
         */
        $classroomCards =
            array_values(
                $classroomCards
            );

        foreach (
            $classroomCards
            as &$classroomCard
        ) {
            $classroomCard['subjects'] =
                array_values(
                    $classroomCard['subjects']
                );
        }

        unset($classroomCard);

        /*
         * Build a unique subject list across all
         * active assignments.
         */
        $mySubjects = [];

        foreach (
            $assignments
            as $assignment
        ) {
            $subjectId = (int) (
                $assignment->subject_id
                ?? 0
            );

            if (
                $subjectId <= 0
                || !isset(
                    $subjectLookup[$subjectId]
                )
            ) {
                continue;
            }

            $mySubjects[$subjectId] =
                $subjectLookup[$subjectId];
        }

        $mySubjects =
            array_values(
                $mySubjects
            );

        /*
         * Calculate total students across assigned classrooms.
         *
         * array_unique prevents counting a classroom twice when
         * the teacher teaches several subjects in that classroom.
         */
        $assignedClassroomIds = array_map(
            static function (
                array $classroom
            ): int {
                return (int) (
                    $classroom['id'] ?? 0
                );
            },
            $classroomCards
        );

        $totalStudents = 0;

        foreach (
            $assignedClassroomIds
            as $classroomId
        ) {
            $totalStudents +=
                $studentCounts[$classroomId]
                ?? 0;
        }

        $firstName = trim(
            (string) (
                $teacher->first_name ?? ''
            )
        );

        $lastName = trim(
            (string) (
                $teacher->last_name ?? ''
            )
        );

        $teacherName = trim(
            $firstName
            . ' '
            . $lastName
        );

        if ($teacherName === '') {
            $teacherName = 'Teacher';
        }

        return $this->view(
            'teacher.dashboard',
            [
                'title' =>
                    'Teacher Dashboard',

                'teacher' =>
                    $teacher,

                'teacherName' =>
                    $teacherName,

                'assignments' =>
                    $assignments,

                'classroomCards' =>
                    $classroomCards,

                'mySubjects' =>
                    $mySubjects,

                'totalStudents' =>
                    $totalStudents,

                'totalClassrooms' =>
                    count(
                        $classroomCards
                    ),

                'totalSubjects' =>
                    count(
                        $mySubjects
                    ),
            ]
        );
    }
}