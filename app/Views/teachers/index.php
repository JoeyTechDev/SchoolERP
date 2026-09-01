<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $teachers
 * @var \SchoolERP\Query\Pagination\Paginator $pagination
 * @var string $search
 */

$teachers = $teachers ?? [];

$search = trim(
    (string) ($search ?? '')
);

$totalTeachers = $pagination->total();
$currentPage = $pagination->currentPage();
$lastPage = $pagination->lastPage();

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

    return '/SchoolERP/public/teachers?'
        . http_build_query($params);
};

/*
|--------------------------------------------------------------------------
| Format status
|--------------------------------------------------------------------------
*/

$formatStatus = static function (
    mixed $value
): array {
    $status = strtolower(
        trim(
            (string) $value
        )
    );

    return match ($status) {
        'active' => [
            'label' => 'Active',
            'class' => 'bg-success-subtle text-success',
        ],

        'inactive' => [
            'label' => 'Inactive',
            'class' => 'bg-secondary-subtle text-secondary',
        ],

        'suspended' => [
            'label' => 'Suspended',
            'class' => 'bg-warning-subtle text-warning-emphasis',
        ],

        'terminated' => [
            'label' => 'Terminated',
            'class' => 'bg-danger-subtle text-danger',
        ],

        default => [
            'label' => $status !== ''
                ? ucfirst($status)
                : 'Unknown',

            'class' => 'bg-light text-dark border',
        ],
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
                Teachers
            </h1>

            <p class="text-muted mb-0">
                Manage teachers and teaching staff.
            </p>

        </div>


        <a
            href="/SchoolERP/public/teachers/create"
            class="btn btn-primary"
        >
            Add Teacher
        </a>

    </div>


    <!-- ============================================================= -->
    <!-- DIRECTORY CARD                                                 -->
    <!-- ============================================================= -->

    <div class="card border-0 shadow-sm">

        <!-- ========================================================= -->
        <!-- HEADER                                                      -->
        <!-- ========================================================= -->

        <div class="card-header bg-white border-bottom py-3">

            <div
                class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3"
            >

                <div>

                    <h2 class="h5 fw-semibold mb-1">
                        Teacher Directory
                    </h2>

                    <p class="text-muted small mb-0">

                        <?= number_format(
                            $totalTeachers
                        ) ?>

                        teacher<?= $totalTeachers === 1
                            ? ''
                            : 's' ?>

                        registered

                    </p>

                </div>


                <!-- Search -->

                <form
                    method="GET"
                    action="/SchoolERP/public/teachers"
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
                            placeholder="Search employee no., name, or email..."
                            aria-label="Search teachers"
                        >

                        <?php if ($search !== ''): ?>

                            <a
                                href="/SchoolERP/public/teachers"
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


        <!-- ========================================================= -->
        <!-- TABLE                                                       -->
        <!-- ========================================================= -->

        <div class="card-body p-0">

            <?php if (empty($teachers)): ?>

                <div
                    class="text-center py-5 px-3"
                >

                    <h5 class="fw-semibold mb-2">
                        <?= $search !== ''
                            ? 'No teachers found'
                            : 'No teachers registered' ?>
                    </h5>


                    <?php if ($search !== ''): ?>

                        <p class="text-muted mb-3">

                            No teacher matches
                            <strong>
                                "<?= htmlspecialchars(
                                    $search,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            </strong>.

                        </p>


                        <a
                            href="/SchoolERP/public/teachers"
                            class="btn btn-outline-secondary"
                        >
                            Clear Search
                        </a>

                    <?php else: ?>

                        <p class="text-muted mb-3">
                            Start by registering your first teacher.
                        </p>


                        <a
                            href="/SchoolERP/public/teachers/create"
                            class="btn btn-primary"
                        >
                            Add First Teacher
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
                                    class="px-4"
                                    style="width: 70px;"
                                >
                                    ID
                                </th>

                                <th
                                    style="min-width: 150px;"
                                >
                                    Employee No.
                                </th>

                                <th
                                    style="min-width: 200px;"
                                >
                                    Teacher
                                </th>

                                <th
                                    style="min-width: 100px;"
                                >
                                    Gender
                                </th>

                                <th
                                    style="min-width: 150px;"
                                >
                                    Phone
                                </th>

                                <th
                                    style="min-width: 220px;"
                                >
                                    Email
                                </th>

                                <th
                                    style="min-width: 120px;"
                                >
                                    Status
                                </th>

                                <th
                                    class="text-end px-4"
                                    style="min-width: 190px;"
                                >
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach (
                                $teachers
                                as $teacher
                            ): ?>

                                <?php

                                $teacherId = (int) (
                                    $teacher['id'] ?? 0
                                );

                                $employeeNumber = trim(
                                    (string) (
                                        $teacher[
                                            'employee_number'
                                        ] ?? ''
                                    )
                                );

                                $firstName = trim(
                                    (string) (
                                        $teacher[
                                            'first_name'
                                        ] ?? ''
                                    )
                                );

                                $lastName = trim(
                                    (string) (
                                        $teacher[
                                            'last_name'
                                        ] ?? ''
                                    )
                                );

                                $fullName = trim(
                                    $firstName
                                    . ' '
                                    . $lastName
                                );

                                $gender = strtolower(
                                    trim(
                                        (string) (
                                            $teacher[
                                                'gender'
                                            ] ?? ''
                                        )
                                    )
                                );

                                $genderLabel = match (
                                    $gender
                                ) {
                                    'male' => 'Male',
                                    'female' => 'Female',
                                    'other' => 'Other',
                                    default => '—',
                                };

                                $phone = trim(
                                    (string) (
                                        $teacher[
                                            'phone'
                                        ] ?? ''
                                    )
                                );

                                $email = trim(
                                    (string) (
                                        $teacher[
                                            'email'
                                        ] ?? ''
                                    )
                                );

                                $status = $formatStatus(
                                    $teacher[
                                        'employment_status'
                                    ] ?? 'active'
                                );

                                ?>

                                <tr>

                                    <!-- ID -->

                                    <td class="px-4">

                                        <span class="text-muted">
                                            #<?= $teacherId ?>
                                        </span>

                                    </td>


                                    <!-- Employee Number -->

                                    <td>

                                        <?php if (
                                            $employeeNumber !== ''
                                        ): ?>

                                            <span
                                                class="badge bg-light text-dark border"
                                            >
                                                <?= htmlspecialchars(
                                                    $employeeNumber,
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


                                    <!-- Teacher -->

                                    <td>

                                        <div class="fw-semibold">

                                            <?= htmlspecialchars(
                                                $fullName !== ''
                                                    ? $fullName
                                                    : 'Unnamed Teacher',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </div>

                                    </td>


                                    <!-- Gender -->

                                    <td>

                                        <?= htmlspecialchars(
                                            $genderLabel,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <!-- Phone -->

                                    <td>

                                        <?php if (
                                            $phone !== ''
                                        ): ?>

                                            <?= htmlspecialchars(
                                                $phone,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- Email -->

                                    <td>

                                        <?php if (
                                            $email !== ''
                                        ): ?>

                                            <span>
                                                <?= htmlspecialchars(
                                                    $email,
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


                                    <!-- Status -->

                                    <td>

                                        <span
                                            class="badge <?= $status['class'] ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $status['label'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                    </td>


                                    <!-- ACTIONS -->

                                    <td class="text-end px-4">

                                        <div
                                            class="d-flex justify-content-end flex-wrap gap-1"
                                        >

                                            <a
                                                href="/SchoolERP/public/teachers/<?= $teacherId ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                View
                                            </a>


                                            <a
                                                href="/SchoolERP/public/teachers/<?= $teacherId ?>/edit"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                Edit
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
        <!-- PAGINATION                                                  -->
        <!-- ========================================================= -->

        <?php if (
            $totalTeachers > 0
            && $lastPage > 1
        ): ?>

            <div class="card-footer bg-white border-top">

                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"
                >

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
                                $totalTeachers
                            ) ?>
                        </strong>

                        teachers

                    </div>


                    <nav aria-label="Teacher pagination">

                        <ul class="pagination pagination-sm mb-0">

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
