<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $students
 * @var \SchoolERP\Query\Pagination\Paginator $pagination
 * @var string $search
 */

$students = $students ?? [];

$search = trim(
    (string) (
        $search ?? ''
    )
);

$totalStudents =
    $pagination->total();

$currentPage =
    $pagination->currentPage();

$lastPage =
    $pagination->lastPage();

/*
|--------------------------------------------------------------------------
| Pagination URL
|--------------------------------------------------------------------------
*/

$pageUrl = static function (
    int $page
) use (
    $search
): string {
    $params = [
        'page' => $page,
    ];

    if ($search !== '') {
        $params['q'] = $search;
    }

    return '/SchoolERP/public/teacher/students?'
        . http_build_query($params);
};

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- HEADER                                                         -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                My Students
            </h1>

            <p class="text-muted mb-0">
                Students belonging to your assigned classrooms.
            </p>

        </div>


        <a
            href="/SchoolERP/public/teacher/dashboard"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- ============================================================= -->
    <!-- DIRECTORY                                                      -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <div
                class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3"
            >

                <div>

                    <h2 class="h5 fw-semibold mb-1">
                        Student Directory
                    </h2>

                    <p class="text-muted small mb-0">

                        <?= number_format(
                            $totalStudents
                        ) ?>

                        student<?= $totalStudents === 1
                            ? ''
                            : 's' ?>

                        available to you

                    </p>

                </div>


                <form
                    method="GET"
                    action="/SchoolERP/public/teacher/students"
                >

                    <div class="input-group">

                        <input
                            type="search"
                            name="q"
                            value="<?= htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control"
                            placeholder="Search name or admission number..."
                            aria-label="Search students"
                        >

                        <?php if (
                            $search !== ''
                        ): ?>

                            <a
                                href="/SchoolERP/public/teacher/students"
                                class="btn btn-outline-secondary"
                            >
                                Clear
                            </a>

                        <?php endif; ?>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Search
                        </button>

                    </div>

                </form>

            </div>

        </div>


        <div class="card-body p-0">

            <?php if (
                empty($students)
            ): ?>

                <div
                    class="text-center py-5 px-3"
                >

                    <h3 class="h6 fw-semibold mb-2">
                        No students found
                    </h3>


                    <?php if (
                        $search !== ''
                    ): ?>

                        <p class="text-muted mb-3">

                            No assigned student matches
                            <strong>
                                "<?= htmlspecialchars(
                                    $search,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            </strong>.

                        </p>

                        <a
                            href="/SchoolERP/public/teacher/students"
                            class="btn btn-outline-secondary"
                        >
                            Clear Search
                        </a>

                    <?php else: ?>

                        <p class="text-muted mb-0">
                            No students currently belong to your assigned classrooms.
                        </p>

                    <?php endif; ?>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table
                        class="table table-hover align-middle mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th
                                    class="px-4"
                                >
                                    ID
                                </th>

                                <th>
                                    Admission No.
                                </th>

                                <th>
                                    Student
                                </th>

                                <th>
                                    Classroom
                                </th>

                                <th
                                    class="text-end px-4"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach (
                                $students
                                as $student
                            ): ?>

                                <?php

                                $studentId = (int) (
                                    $student['id']
                                    ?? 0
                                );

                                $admissionNumber =
                                    trim(
                                        (string) (
                                            $student[
                                                'admission_number'
                                            ] ?? ''
                                        )
                                    );

                                $firstName =
                                    trim(
                                        (string) (
                                            $student[
                                                'first_name'
                                            ] ?? ''
                                        )
                                    );

                                $lastName =
                                    trim(
                                        (string) (
                                            $student[
                                                'last_name'
                                            ] ?? ''
                                        )
                                    );

                                $studentName =
                                    trim(
                                        $firstName
                                        . ' '
                                        . $lastName
                                    );

                                if (
                                    $studentName === ''
                                ) {
                                    $studentName =
                                        'Unnamed Student';
                                }

                                $classroomName =
                                    (string) (
                                        $student[
                                            'classroom_name'
                                        ]
                                        ?? 'Unknown Classroom'
                                    );

                                ?>

                                <tr>

                                    <td class="px-4">
                                        #<?= $studentId ?>
                                    </td>


                                    <td>

                                        <?php if (
                                            $admissionNumber !== ''
                                        ): ?>

                                            <span
                                                class="badge bg-light text-dark border"
                                            >

                                                <?= htmlspecialchars(
                                                    $admissionNumber,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $studentName,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </div>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $classroomName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <td class="text-end px-4">

                                        <a
                                            href="/SchoolERP/public/teacher/students/<?= $studentId ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>


        <!-- ========================================================= -->
        <!-- PAGINATION                                                  -->
        <!-- ========================================================= -->

        <?php if (
            $totalStudents > 0
            && $lastPage > 1
        ): ?>

            <div class="card-footer bg-white border-top">

                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"
                >

                    <div class="small text-muted">

                        Showing

                        <strong>
                            <?= $pagination->firstItem() ?>
                        </strong>

                        to

                        <strong>
                            <?= $pagination->lastItem() ?>
                        </strong>

                        of

                        <strong>
                            <?= number_format(
                                $totalStudents
                            ) ?>
                        </strong>

                    </div>


                    <nav
                        aria-label="My students pagination"
                    >

                        <ul
                            class="pagination pagination-sm mb-0"
                        >

                            <li
                                class="page-item <?= !$pagination->hasPreviousPage()
                                    ? 'disabled'
                                    : '' ?>"
                            >

                                <?php if (
                                    $pagination->hasPreviousPage()
                                ): ?>

                                    <a
                                        class="page-link"
                                        href="<?= htmlspecialchars(
                                            $pageUrl(
                                                $pagination->previousPage()
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >
                                        Previous
                                    </a>

                                <?php else: ?>

                                    <span class="page-link">
                                        Previous
                                    </span>

                                <?php endif; ?>

                            </li>


                            <?php for (
                                $page = 1;
                                $page <= $lastPage;
                                $page++
                            ): ?>

                                <li
                                    class="page-item <?= $page === $currentPage
                                        ? 'active'
                                        : '' ?>"
                                >

                                    <?php if (
                                        $page === $currentPage
                                    ): ?>

                                        <span class="page-link">
                                            <?= $page ?>
                                        </span>

                                    <?php else: ?>

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                $pageUrl($page),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                            <?= $page ?>
                                        </a>

                                    <?php endif; ?>

                                </li>

                            <?php endfor; ?>


                            <li
                                class="page-item <?= !$pagination->hasMorePages()
                                    ? 'disabled'
                                    : '' ?>"
                            >

                                <?php if (
                                    $pagination->hasMorePages()
                                ): ?>

                                    <a
                                        class="page-link"
                                        href="<?= htmlspecialchars(
                                            $pageUrl(
                                                $pagination->nextPage()
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >
                                        Next
                                    </a>

                                <?php else: ?>

                                    <span class="page-link">
                                        Next
                                    </span>

                                <?php endif; ?>

                            </li>

                        </ul>

                    </nav>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>