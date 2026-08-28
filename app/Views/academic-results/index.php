<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $students
 * @var array<int,array<string,mixed>> $subjects
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var array<int,array<string,mixed>> $results
 * @var int $studentId
 * @var int $sessionId
 * @var int $termId
 */

$students = $students ?? [];
$subjects = $subjects ?? [];
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$results = $results ?? [];

$studentId = (int) ($studentId ?? 0);
$sessionId = (int) ($sessionId ?? 0);
$termId = (int) ($termId ?? 0);

/*
 * Build subject lookup for the result table.
 */
$subjectLookup = [];

foreach ($subjects as $subject) {
    $subjectLookup[(int) $subject['id']] =
        (string) $subject['name'];
}

/*
 * Build student lookup.
 */
$studentLookup = [];

foreach ($students as $student) {
    $studentLookup[(int) $student['id']] =
        trim(
            (string) $student['first_name']
            . ' '
            . (string) $student['last_name']
        );
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Academic Results
            </h1>

            <p class="text-muted mb-0">
                View and manage student academic results.
            </p>
        </div>

        <a
            href="/SchoolERP/public/academic-results/create"
            class="btn btn-primary"
        >
            + Enter Result
        </a>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="/SchoolERP/public/academic-results"
            >

                <div class="row g-3 align-items-end">

                    <div class="col-md-4">

                        <label
                            for="student_id"
                            class="form-label"
                        >
                            Student
                        </label>

                        <select
                            id="student_id"
                            name="student_id"
                            class="form-select"
                        >

                            <option value="">
                                -- Select Student --
                            </option>

                            <?php foreach ($students as $student): ?>

                                <?php
                                $id = (int) $student['id'];

                                $name = trim(
                                    (string) $student['first_name']
                                    . ' '
                                    . (string) $student['last_name']
                                );
                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $studentId === $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $name,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label
                            for="academic_session_id"
                            class="form-label"
                        >
                            Academic Session
                        </label>

                        <select
                            id="academic_session_id"
                            name="academic_session_id"
                            class="form-select"
                        >

                            <option value="">
                                -- Select Session --
                            </option>

                            <?php foreach ($sessions as $session): ?>

                                <?php $id = (int) $session['id']; ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $sessionId === $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) $session['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label
                            for="term_id"
                            class="form-label"
                        >
                            Term
                        </label>

                        <select
                            id="term_id"
                            name="term_id"
                            class="form-select"
                        >

                            <option value="">
                                -- Select Term --
                            </option>

                            <?php foreach ($terms as $term): ?>

                                <?php $id = (int) $term['id']; ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $termId === $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) $term['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-outline-primary w-100"
                        >
                            View Results
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <?php if (
        $studentId > 0
        && $sessionId > 0
        && $termId > 0
    ): ?>

        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <div class="fw-semibold">

                    <?= htmlspecialchars(
                        $studentLookup[$studentId]
                        ?? 'Selected Student',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            </div>

            <div class="card-body p-0">

                <?php if ($results === []): ?>

                    <div class="p-4">

                        <div class="alert alert-info mb-0">
                            No results have been entered for this
                            student, session, and term.
                        </div>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Subject
                                    </th>

                                    <th>
                                        CA / 30
                                    </th>

                                    <th>
                                        Exam / 70
                                    </th>

                                    <th>
                                        Total / 100
                                    </th>

                                    <th>
                                        Grade
                                    </th>

                                    <th>
                                        Remark
                                    </th>

                                    <th class="text-end">
                                        Actions
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($results as $result): ?>

                                    <?php
                                    $resultId = (int) (
                                        $result['id'] ?? 0
                                    );

                                    $subjectId = (int) (
                                        $result['subject_id'] ?? 0
                                    );

                                    $total = $result['total_score']
                                        ?? null;

                                    $grade = $result['grade']
                                        ?? '';

                                    $remark = $result['remark']
                                        ?? '';
                                    ?>

                                    <tr>

                                        <td>
                                            <strong>
                                                <?= htmlspecialchars(
                                                    $subjectLookup[
                                                        $subjectId
                                                    ] ?? 'Unknown Subject',
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['ca_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['exam_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= htmlspecialchars(
                                                    (string) (
                                                        $total ?? '—'
                                                    ),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </strong>
                                        </td>

                                        <td>

                                            <?php if ($grade !== ''): ?>

                                                <span class="badge text-bg-primary">
                                                    <?= htmlspecialchars(
                                                        (string) $grade,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                </span>

                                            <?php else: ?>

                                                —

                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                (string) (
                                                    $remark ?: '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>

                                        <td class="text-end">

                                            <a
                                                href="/SchoolERP/public/academic-results/<?= $resultId ?>/edit"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="/SchoolERP/public/academic-results/<?= $resultId ?>/delete"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this result?');"
                                            >

                                                <?= csrf_field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <h2 class="h5">
                    Select a student, session, and term
                </h2>

                <p class="text-muted mb-0">
                    Choose the filters above to view academic results.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>