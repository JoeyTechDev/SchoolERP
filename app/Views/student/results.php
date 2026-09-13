<?php

declare(strict_types=1);

/**
 * @var \SchoolERP\Models\Student $student
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var array<int,array<string,mixed>> $subjects
 * @var array<int,array<string,mixed>> $results
 * @var int $sessionId
 * @var int $termId
 */

$student = $student ?? null;
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$subjects = $subjects ?? [];
$results = $results ?? [];

$sessionId = (int) ($sessionId ?? 0);
$termId = (int) ($termId ?? 0);

$studentName = 'Student';

if ($student !== null) {
    $studentName = trim(
        (string) (
            $student->first_name ?? ''
        )
        . ' '
        . (string) (
            $student->last_name ?? ''
        )
    );

    if ($studentName === '') {
        $studentName = 'Student';
    }
}

/*
 * Build subject lookup.
 */
$subjectLookup = [];

foreach ($subjects as $subject) {
    $id = (int) (
        $subject['id'] ?? 0
    );

    if ($id <= 0) {
        continue;
    }

    $subjectLookup[$id] = [
        'name' => (string) (
            $subject['name'] ?? ''
        ),
        'code' => (string) (
            $subject['code'] ?? ''
        ),
    ];
}

/*
 * Find selected session and term names.
 */
$sessionName = 'Not selected';
$termName = 'Not selected';

foreach ($sessions as $session) {
    if (
        (int) (
            $session['id'] ?? 0
        ) === $sessionId
    ) {
        $sessionName = (string) (
            $session['name'] ?? ''
        );

        break;
    }
}

foreach ($terms as $term) {
    if (
        (int) (
            $term['id'] ?? 0
        ) === $termId
    ) {
        $termName = (string) (
            $term['name'] ?? ''
        );

        break;
    }
}

/*
 * Calculate student summary.
 */
$resultCount = count($results);
$totalScore = 0;

foreach ($results as $result) {
    $totalScore += (float) (
        $result['total_score'] ?? 0
    );
}

$averageScore =
    $resultCount > 0
        ? $totalScore / $resultCount
        : 0;
?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div
        class="d-flex flex-column flex-md-row
               justify-content-between
               align-items-md-center
               gap-3
               mb-4"
    >

        <div>

            <h1 class="h3 fw-bold mb-1">
                My Results
            </h1>

            <p class="text-muted mb-0">
                <?= htmlspecialchars(
                    $studentName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
                — academic performance
            </p>

        </div>

        <a
            href="/SchoolERP/public/student/dashboard"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>

    </div>


    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="/SchoolERP/public/student/results"
            >

                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md-5">

                        <label
                            for="academic_session_id"
                            class="form-label fw-semibold"
                        >
                            Academic Session
                        </label>

                        <select
                            id="academic_session_id"
                            name="academic_session_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select session
                            </option>

                            <?php foreach (
                                $sessions
                                as $session
                            ): ?>

                                <?php
                                $id = (int) (
                                    $session['id'] ?? 0
                                );
                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $id === $sessionId
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) (
                                            $session['name'] ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="col-12 col-md-5">

                        <label
                            for="term_id"
                            class="form-label fw-semibold"
                        >
                            Term
                        </label>

                        <select
                            id="term_id"
                            name="term_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select term
                            </option>

                            <?php foreach (
                                $terms
                                as $term
                            ): ?>

                                <?php
                                $id = (int) (
                                    $term['id'] ?? 0
                                );
                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $id === $termId
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) (
                                            $term['name'] ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div class="col-12 col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            View Results
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <?php if (
        $sessionId > 0
        && $termId > 0
    ): ?>

        <!-- Selected Period -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-12 col-md-6">

                        <div class="small text-muted">
                            Academic Session
                        </div>

                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                $sessionName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                    <div class="col-12 col-md-6">

                        <div class="small text-muted">
                            Term
                        </div>

                        <div class="fw-semibold">
                            <?= htmlspecialchars(
                                $termName,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Summary -->
        <div class="row g-3 mb-4">

            <div class="col-12 col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Subjects Recorded
                        </div>

                        <div class="fs-3 fw-bold">
                            <?= $resultCount ?>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-12 col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Total Score
                        </div>

                        <div class="fs-3 fw-bold">
                            <?= number_format(
                                $totalScore,
                                2
                            ) ?>
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-12 col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted">
                            Average Score
                        </div>

                        <div class="fs-3 fw-bold">
                            <?= number_format(
                                $averageScore,
                                2
                            ) ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Result Table -->
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-bottom">

                <h2 class="h5 fw-semibold mb-0">
                    Academic Results
                </h2>

            </div>

            <div class="card-body">

                <?php if (
                    $results === []
                ): ?>

                    <div class="text-center py-5">

                        <h3 class="h5 mb-2">
                            No Results Available
                        </h3>

                        <p class="text-muted mb-0">
                            No academic results have been recorded
                            for this session and term.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle mb-0"
                        >

                            <thead>

                                <tr>

                                    <th>
                                        Subject
                                    </th>

                                    <th class="text-center">
                                        CA
                                    </th>

                                    <th class="text-center">
                                        Exam
                                    </th>

                                    <th class="text-center">
                                        Total
                                    </th>

                                    <th class="text-center">
                                        Grade
                                    </th>

                                    <th>
                                        Remark
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach (
                                    $results
                                    as $result
                                ): ?>

                                    <?php
                                    $subjectId = (int) (
                                        $result['subject_id']
                                        ?? 0
                                    );

                                    $subject =
                                        $subjectLookup[
                                            $subjectId
                                        ]
                                        ?? null;

                                    $subjectName =
                                        $subject['name']
                                        ?? (
                                            (string) (
                                                $result['subject']
                                                ?? 'Unknown Subject'
                                            )
                                        );

                                    $subjectCode =
                                        $subject['code']
                                        ?? '';

                                    $grade = trim(
                                        (string) (
                                            $result['grade']
                                            ?? ''
                                        )
                                    );

                                    $remark = trim(
                                        (string) (
                                            $result['remark']
                                            ?? ''
                                        )
                                    );
                                    ?>

                                    <tr>

                                        <td>

                                            <div class="fw-semibold">

                                                <?= htmlspecialchars(
                                                    $subjectName,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </div>

                                            <?php if (
                                                $subjectCode !== ''
                                            ): ?>

                                                <div class="small text-muted">

                                                    <?= htmlspecialchars(
                                                        $subjectCode,
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </div>

                                            <?php endif; ?>

                                        </td>


                                        <td class="text-center">

                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['ca_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>


                                        <td class="text-center">

                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['exam_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>


                                        <td class="text-center fw-bold">

                                            <?= htmlspecialchars(
                                                (string) (
                                                    $result['total_score']
                                                    ?? '—'
                                                ),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>


                                        <td class="text-center">

                                            <?php if (
                                                $grade !== ''
                                            ): ?>

                                                <span
                                                    class="badge text-bg-primary"
                                                >
                                                    <?= htmlspecialchars(
                                                        $grade,
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
                                                $remark !== ''
                                                    ? $remark
                                                    : '—',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

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

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <h2 class="h5 mb-2">
                    Select Your Academic Period
                </h2>

                <p class="text-muted mb-0">
                    Select an academic session and term
                    to view your results.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>