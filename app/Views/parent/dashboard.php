<?php

declare(strict_types=1);

/**
 * SchoolERP - Parent Dashboard
 *
 * Displays only students linked to the authenticated parent.
 */

$students = $students ?? [];

$escape = static function (mixed $value): string {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Parent Dashboard</h1>
            <p class="text-muted mb-0">
                View the students linked to your parent account.
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0">My Children</h2>
        </div>

        <div class="card-body">
            <?php if ($students === []): ?>
                <div class="alert alert-info mb-0" role="status">
                    No students are currently linked to your account.
                    Please contact the school administrator if you believe
                    this is incorrect.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Student Name</th>
                                <th scope="col">Admission Number</th>
                                <th scope="col">Class</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <td>
                                        <?= $escape($index + 1) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            trim(
                                                ($student['first_name'] ?? '')
                                                . ' '
                                                . ($student['last_name'] ?? '')
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            $student['admission_number'] ?? '—'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            $student['classroom_name'] ?? '—'
                                        ) ?>
                                    </td>

                                    <td>
                                        <a
                                            class="btn btn-sm btn-primary"
                                            href="<?= $escape(
                                                '/SchoolERP/public/parent/students/'
                                                . (int) ($student['id'] ?? 0)
                                            ) ?>"
                                        >
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
