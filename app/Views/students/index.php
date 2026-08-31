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

$totalStudents = $pagination->total();

$currentPage = $pagination->currentPage();

$lastPage = $pagination->lastPage();

/*
|--------------------------------------------------------------------------
| Pagination URL helper
|--------------------------------------------------------------------------
|
| Preserve the active search query while moving between pages.
|
*/

$pageUrl = static function (
    int $page
) use (
    $search
): string {
    $query = [
        'page' => $page,
    ];

    if ($search !== '') {
        $query['q'] = $search;
    }

    return '/SchoolERP/public/students?'
        . http_build_query($query);
};

/*
|--------------------------------------------------------------------------
| Date formatter
|--------------------------------------------------------------------------
*/

$formatDate = static function (
    mixed $value
): string {

    if ($value === null || $value === '') {
        return '—';
    }

    if (
        $value instanceof \DateTimeInterface
    ) {
        return $value->format('d M Y');
    }

    $value = trim(
        (string) $value
    );

    if ($value === '') {
        return '—';
    }

    $timestamp = strtotime($value);

    if ($timestamp === false) {
        return $value;
    }

    return date(
        'd M Y',
        $timestamp
    );
};

/*
|--------------------------------------------------------------------------
| Gender formatter
|--------------------------------------------------------------------------
*/

$formatGender = static function (
    mixed $value
): string {

    return match (
        strtolower(
            trim(
                (string) $value
            )
        )
    ) {
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
        default => '—',
    };
};

?>

<div class="container-fluid py-4">

    <!-- ============================================================= -->
    <!-- PAGE HEADER                                                    -->
    <!-- ============================================================= -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                Students
            </h1>

            <p class="text-muted mb-0">
                Manage students enrolled in the school.
            </p>

        </div>


        <a
            href="/SchoolERP/public/students/create"
            class="btn btn-primary"
        >
            <i class="bi bi-person-plus me-1"></i>
            Add Student
        </a>

    </div>


    <!-- ============================================================= -->
    <!-- DIRECTORY CARD                                                 -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <!-- ========================================================= -->
        <!-- CARD HEADER                                                 -->
        <!-- ========================================================= -->

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

                        registered

                    </p>

                </div>


                <!-- Search -->
                <form
                    method="GET"
                    action="/SchoolERP/public/students"
                    class="d-flex"
                    role="search"
                >

                    <div class="input-group">

                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="search"
                            name="q"
                            value="<?= htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            class="form-control"
                            placeholder="Search admission no. or name..."
                            aria-label="Search students"
                        >

                        <?php if ($search !== ''): ?>

                            <a
                                href="/SchoolERP/public/students"
                                class="btn btn-outline-secondary"
                                title="Clear search"
                            >
                                <i class="bi bi-x-lg"></i>
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


            <?php if ($search !== ''): ?>

                <div class="mt-3">

                    <span class="badge text-bg-light border">

                        Search:
                        <strong>
                            <?= htmlspecialchars(
                                $search,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </strong>

                    </span>

                </div>

            <?php endif; ?>

        </div>


        <!-- ========================================================= -->
        <!-- TABLE                                                       -->
        <!-- ========================================================= -->

        <div class="card-body p-0">

            <?php if (empty($students)): ?>

                <div class="text-center py-5 px-3">

                    <div class="mb-3">

                        <i
                            class="bi bi-people fs-1 text-muted"
                            aria-hidden="true"
                        ></i>

                    </div>


                    <?php if ($search !== ''): ?>

                        <h5 class="fw-semibold">
                            No students found
                        </h5>

                        <p class="text-muted mb-3">

                            No student matches
                            <strong>
                                "<?= htmlspecialchars(
                                    $search,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            </strong>.

                        </p>

                        <a
                            href="/SchoolERP/public/students"
                            class="btn btn-outline-secondary"
                        >
                            Clear Search
                        </a>

                    <?php else: ?>

                        <h5 class="fw-semibold">
                            No students found
                        </h5>

                        <p class="text-muted mb-3">
                            There are currently no students registered.
                        </p>

                        <a
                            href="/SchoolERP/public/students/create"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-person-plus me-1"></i>
                            Add First Student
                        </a>

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
                                    scope="col"
                                    class="px-4"
                                    style="width: 70px;"
                                >
                                    ID
                                </th>

                                <th
                                    scope="col"
                                    style="min-width: 140px;"
                                >
                                    Admission No.
                                </th>

                                <th
                                    scope="col"
                                    style="min-width: 180px;"
                                >
                                    Student
                                </th>

                                <th
                                    scope="col"
                                    style="width: 110px;"
                                >
                                    Gender
                                </th>

                                <th
                                    scope="col"
                                    style="min-width: 140px;"
                                >
                                    Date of Birth
                                </th>

                                <th
                                    scope="col"
                                    style="min-width: 150px;"
                                >
                                    Classroom
                                </th>

                                <th
                                    scope="col"
                                    class="text-end px-4"
                                    style="width: 190px;"
                                >
                                    Actions
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
                                    $student['id'] ?? 0
                                );

                                $firstName = trim(
                                    (string) (
                                        $student['first_name']
                                        ?? ''
                                    )
                                );

                                $lastName = trim(
                                    (string) (
                                        $student['last_name']
                                        ?? ''
                                    )
                                );

                                $fullName = trim(
                                    $firstName
                                    . ' '
                                    . $lastName
                                );

                                $admissionNumber = trim(
                                    (string) (
                                        $student[
                                            'admission_number'
                                        ]
                                        ?? ''
                                    )
                                );

                                $gender = $formatGender(
                                    $student['gender'] ?? null
                                );

                                $dateOfBirth = $formatDate(
                                    $student[
                                        'date_of_birth'
                                    ] ?? null
                                );

                                /*
                                 * Prefer the classroom name supplied by
                                 * StudentRepository. Fall back to the ID
                                 * when only classroom_id is available.
                                 */
                                $classroomName = trim(
                                    (string) (
                                        $student[
                                            'classroom_name'
                                        ]
                                        ?? ''
                                    )
                                );

                                $classroomId = $student[
                                    'classroom_id'
                                ] ?? null;

                                if (
                                    $classroomName === ''
                                ) {
                                    if (
                                        $classroomId !== null
                                        && $classroomId !== ''
                                    ) {
                                        $classroomName =
                                            'Classroom #'
                                            . (int) $classroomId;
                                    } else {
                                        $classroomName =
                                            'Not assigned';
                                    }
                                }

                                ?>


                                <tr>

                                    <!-- ================================================= -->
                                    <!-- ID                                                -->
                                    <!-- ================================================= -->

                                    <td class="px-4">

                                        <span class="text-muted">
                                            #<?= $studentId ?>
                                        </span>

                                    </td>


                                    <!-- ================================================= -->
                                    <!-- ADMISSION NUMBER                                  -->
                                    <!-- ================================================= -->

                                    <td>

                                        <?php if (
                                            $admissionNumber !== ''
                                        ): ?>

                                            <span
                                                class="badge text-bg-light border fw-semibold"
                                            >

                                                <?= htmlspecialchars(
                                                    $admissionNumber,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Not assigned
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- ================================================= -->
                                    <!-- STUDENT                                           -->
                                    <!-- ================================================= -->

                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $fullName !== ''
                                                    ? $fullName
                                                    : 'Unnamed Student',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </div>

                                    </td>


                                    <!-- ================================================= -->
                                    <!-- GENDER                                            -->
                                    <!-- ================================================= -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $gender,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <!-- ================================================= -->
                                    <!-- DATE OF BIRTH                                     -->
                                    <!-- ================================================= -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $dateOfBirth,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <!-- ================================================= -->
                                    <!-- CLASSROOM                                         -->
                                    <!-- ================================================= -->

                                    <td>

                                        <?php if (
                                            $classroomName
                                            !== 'Not assigned'
                                        ): ?>

                                            <span
                                                class="badge text-bg-light border"
                                            >

                                                <?= htmlspecialchars(
                                                    $classroomName,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Not assigned
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- ================================================= -->
                                    <!-- ACTIONS                                            -->
                                    <!-- ================================================= -->

                                    <td class="text-end px-4">

                                        <div
                                            class="btn-group"
                                            role="group"
                                            aria-label="Student actions"
                                        >

                                            <a
                                                href="/SchoolERP/public/students/<?= $studentId ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="View Student"
                                            >
                                                <i class="bi bi-eye"></i>
                                                <span class="d-none d-xl-inline ms-1">
                                                    View
                                                </span>
                                            </a>


                                            <a
                                                href="/SchoolERP/public/students/<?= $studentId ?>/edit"
                                                class="btn btn-sm btn-outline-secondary"
                                                title="Edit Student"
                                            >
                                                <i class="bi bi-pencil"></i>
                                                <span class="d-none d-xl-inline ms-1">
                                                    Edit
                                                </span>
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>


        <!-- ========================================================= -->
        <!-- PAGINATION                                                 -->
        <!-- ========================================================= -->

        <?php if ($totalStudents > 0): ?>

            <div class="card-footer bg-white border-top">

                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"
                >

                    <!-- Showing -->
                    <div class="text-muted small">

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

                        students

                    </div>


                    <!-- Page indicator -->
                    <div class="text-muted small">

                        Page

                        <strong>
                            <?= $currentPage ?>
                        </strong>

                        of

                        <strong>
                            <?= $lastPage ?>
                        </strong>

                    </div>


                    <?php if ($lastPage > 1): ?>

                        <nav
                            aria-label="Student pagination"
                        >

                            <ul class="pagination pagination-sm mb-0">


                                <!-- ================================================= -->
                                <!-- PREVIOUS                                            -->
                                <!-- ================================================= -->

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
                                            aria-label="Previous"
                                        >
                                            <i
                                                class="bi bi-chevron-left"
                                            ></i>
                                        </a>

                                    <?php else: ?>

                                        <span
                                            class="page-link"
                                            aria-hidden="true"
                                        >
                                            <i
                                                class="bi bi-chevron-left"
                                            ></i>
                                        </span>

                                    <?php endif; ?>

                                </li>


                                <!-- ================================================= -->
                                <!-- PAGE NUMBERS                                       -->
                                <!-- ================================================= -->

                                <?php

                                $startPage = max(
                                    1,
                                    $currentPage - 2
                                );

                                $endPage = min(
                                    $lastPage,
                                    $currentPage + 2
                                );

                                ?>

                                <?php if (
                                    $startPage > 1
                                ): ?>

                                    <li class="page-item">

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                $pageUrl(1),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                            1
                                        </a>

                                    </li>


                                    <?php if (
                                        $startPage > 2
                                    ): ?>

                                        <li
                                            class="page-item disabled"
                                        >

                                            <span class="page-link">
                                                …
                                            </span>

                                        </li>

                                    <?php endif; ?>

                                <?php endif; ?>


                                <?php for (
                                    $page = $startPage;
                                    $page <= $endPage;
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

                                            <span
                                                class="page-link"
                                                aria-current="page"
                                            >
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


                                <?php if (
                                    $endPage < $lastPage
                                ): ?>

                                    <?php if (
                                        $endPage
                                        < $lastPage - 1
                                    ): ?>

                                        <li
                                            class="page-item disabled"
                                        >

                                            <span class="page-link">
                                                …
                                            </span>

                                        </li>

                                    <?php endif; ?>


                                    <li class="page-item">

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                $pageUrl($lastPage),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >
                                            <?= $lastPage ?>
                                        </a>

                                    </li>

                                <?php endif; ?>


                                <!-- ================================================= -->
                                <!-- NEXT                                                -->
                                <!-- ================================================= -->

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
                                            aria-label="Next"
                                        >
                                            <i
                                                class="bi bi-chevron-right"
                                            ></i>
                                        </a>

                                    <?php else: ?>

                                        <span
                                            class="page-link"
                                            aria-hidden="true"
                                        >
                                            <i
                                                class="bi bi-chevron-right"
                                            ></i>
                                        </span>

                                    <?php endif; ?>

                                </li>

                            </ul>

                        </nav>

                    <?php endif; ?>

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>