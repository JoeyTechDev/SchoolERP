<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $students
 * @var \SchoolERP\Query\Pagination\Paginator $pagination
 */
?>

<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

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
            + Add Student
        </a>

    </div>


    <!-- Student Directory Card -->
    <div class="card border-0 shadow-sm">

        <!-- Card Header -->
        <div class="card-header bg-white border-bottom py-3">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

                <div>

                    <h2 class="h5 fw-semibold mb-1">
                        Student Directory
                    </h2>

                    <p class="text-muted small mb-0">

                        <?= number_format($pagination->total()) ?>

                        student<?= $pagination->total() === 1 ? '' : 's' ?>

                        registered

                    </p>

                </div>


                <div class="text-muted small">

                    Page
                    <strong>
                        <?= $pagination->currentPage() ?>
                    </strong>

                    of

                    <strong>
                        <?= $pagination->lastPage() ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- Table -->
        <div class="card-body p-0">

            <?php if (empty($students)): ?>

                <div class="text-center py-5">

                    <div class="mb-3">

                        <span class="fs-1">
                            👨‍🎓
                        </span>

                    </div>

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
                        Add First Student
                    </a>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th
                                    scope="col"
                                    class="px-4"
                                    style="width: 80px;"
                                >
                                    ID
                                </th>

                                <th scope="col">
                                    First Name
                                </th>

                                <th scope="col">
                                    Last Name
                                </th>

                                <th scope="col">
                                    Classroom
                                </th>

                                <th
                                    scope="col"
                                    class="text-end px-4"
                                    style="width: 180px;"
                                >
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($students as $student): ?>

                                <tr>

                                    <!-- ID -->
                                    <td class="px-4">

                                        <span class="text-muted">
                                            #<?= (int) $student['id'] ?>
                                        </span>

                                    </td>


                                    <!-- First Name -->
                                    <td>

                                        <span class="fw-semibold">

                                            <?= htmlspecialchars(
                                                (string) $student['first_name'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- Last Name -->
                                    <td>

                                        <?= htmlspecialchars(
                                            (string) $student['last_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </td>


                                    <!-- Classroom -->
                                    <td>

                                        <?php
                                        $classroomId = $student['classroom_id'] ?? null;
                                        ?>

                                        <?php if ($classroomId !== null && $classroomId !== ''): ?>

                                            <span class="badge text-bg-light border">

                                                Classroom
                                                <?= (int) $classroomId ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Not assigned
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <!-- Actions -->
<td class="text-end px-4">

    <div class="btn-group" role="group">

        <a
            href="/SchoolERP/public/students/<?= (int) $student['id'] ?>"
            class="btn btn-sm btn-outline-primary"
        >
            View
        </a>

        <a
            href="/SchoolERP/public/students/<?= (int) $student['id'] ?>/edit"
            class="btn btn-sm btn-outline-secondary"
        >
            Edit
        </a>

        <form
            method="POST"
            action="/SchoolERP/public/students/<?= (int) $student['id'] ?>/delete"
            class="d-inline"
            onsubmit="return confirm('Are you sure you want to delete this student?');"
        >
            <?= csrf_field() ?>

            <button
                type="submit"
                class="btn btn-sm btn-outline-danger"
            >
                Delete
            </button>
        </form>

    </div>

</td>
                                    

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>


        <!-- Footer / Pagination -->
        <?php if ($pagination->total() > 0): ?>

            <div class="card-footer bg-white border-top">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

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
                            <?= $pagination->total() ?>
                        </strong>

                        students

                    </div>


                    <!-- Pagination -->
                    <?php if ($pagination->lastPage() > 1): ?>

                        <nav aria-label="Student pagination">

                            <ul class="pagination pagination-sm mb-0">

                                <!-- Previous -->
                                <li
                                    class="page-item
                                    <?= !$pagination->hasPreviousPage()
                                        ? 'disabled'
                                        : '' ?>"
                                >

                                    <?php if ($pagination->hasPreviousPage()): ?>

                                        <a
                                            class="page-link"
                                            href="/SchoolERP/public/students?page=<?= $pagination->previousPage() ?>"
                                            aria-label="Previous"
                                        >
                                            &laquo;
                                        </a>

                                    <?php else: ?>

                                        <span class="page-link">
                                            &laquo;
                                        </span>

                                    <?php endif; ?>

                                </li>


                                <!-- Page Numbers -->
                                <?php for (
                                    $page = 1;
                                    $page <= $pagination->lastPage();
                                    $page++
                                ): ?>

                                    <li
                                        class="page-item
                                        <?= $page === $pagination->currentPage()
                                            ? 'active'
                                            : '' ?>"
                                    >

                                        <?php if (
                                            $page === $pagination->currentPage()
                                        ): ?>

                                            <span class="page-link">
                                                <?= $page ?>
                                            </span>

                                        <?php else: ?>

                                            <a
                                                class="page-link"
                                                href="/SchoolERP/public/students?page=<?= $page ?>"
                                            >
                                                <?= $page ?>
                                            </a>

                                        <?php endif; ?>

                                    </li>

                                <?php endfor; ?>


                                <!-- Next -->
                                <li
                                    class="page-item
                                    <?= !$pagination->hasMorePages()
                                        ? 'disabled'
                                        : '' ?>"
                                >

                                    <?php if ($pagination->hasMorePages()): ?>

                                        <a
                                            class="page-link"
                                            href="/SchoolERP/public/students?page=<?= $pagination->nextPage() ?>"
                                            aria-label="Next"
                                        >
                                            &raquo;
                                        </a>

                                    <?php else: ?>

                                        <span class="page-link">
                                            &raquo;
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