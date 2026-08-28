<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var array<int,array<string,mixed>> $students
 * @var array<int,array<string,mixed>> $subjects
 * @var array<int,array<string,mixed>> $sessions
 * @var array<int,array<string,mixed>> $terms
 * @var \SchoolERP\Models\AcademicSession|null $currentSession
 */

$students = $students ?? [];
$subjects = $subjects ?? [];
$sessions = $sessions ?? [];
$terms = $terms ?? [];
$currentSession = $currentSession ?? null;

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$oldInput = $session->flash('_old_input') ?? [];
$errors = $session->flash('_errors') ?? [];

$oldInput = is_array($oldInput)
    ? $oldInput
    : [];

$errors = is_array($errors)
    ? $errors
    : [];

$studentId = (string) (
    $oldInput['student_id'] ?? ''
);

$subjectId = (string) (
    $oldInput['subject_id'] ?? ''
);

$academicSessionId = (string) (
    $oldInput['academic_session_id']
    ?? (
        $currentSession !== null
            ? $currentSession->id
            : ''
    )
);

$termId = (string) (
    $oldInput['term_id'] ?? ''
);

$caScore = (string) (
    $oldInput['ca_score'] ?? ''
);

$examScore = (string) (
    $oldInput['exam_score'] ?? ''
);

$studentError = $errors['student_id'] ?? null;
$subjectError = $errors['subject_id'] ?? null;
$sessionError = $errors['academic_session_id'] ?? null;
$termError = $errors['term_id'] ?? null;
$caError = $errors['ca_score'] ?? null;
$examError = $errors['exam_score'] ?? null;

$formatError = static function ($error): string {
    if (is_array($error)) {
        return implode(', ', $error);
    }

    return (string) $error;
};
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Enter Academic Result
            </h1>

            <p class="text-muted mb-0">
                CA is out of 30. Examination is out of 70.
            </p>

        </div>

        <a
            href="/SchoolERP/public/academic-results"
            class="btn btn-secondary"
        >
            Back to Results
        </a>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form
                method="POST"
                action="/SchoolERP/public/academic-results"
                id="resultForm"
            >

                <?= csrf_field() ?>

                <div class="row g-3">

                    <div class="col-md-6">

                        <label
                            for="student_id"
                            class="form-label"
                        >
                            Student
                        </label>

                        <select
                            id="student_id"
                            name="student_id"
                            class="form-select <?= $studentError !== null ? 'is-invalid' : '' ?>"
                            required
                        >

                            <option value="">
                                -- Select Student --
                            </option>

                            <?php foreach ($students as $student): ?>

                                <?php
                                $id = (int) $student['id'];

                                $studentName = trim(
                                    (string) $student['first_name']
                                    . ' '
                                    . (string) $student['last_name']
                                );
                                ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $studentId === (string) $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        $studentName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?php if ($studentError !== null): ?>

                            <div class="invalid-feedback">
                                <?= htmlspecialchars(
                                    $formatError($studentError),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">

                        <label
                            for="subject_id"
                            class="form-label"
                        >
                            Subject
                        </label>

                        <select
                            id="subject_id"
                            name="subject_id"
                            class="form-select <?= $subjectError !== null ? 'is-invalid' : '' ?>"
                            required
                        >

                            <option value="">
                                -- Select Subject --
                            </option>

                            <?php foreach ($subjects as $subject): ?>

                                <?php $id = (int) $subject['id']; ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $subjectId === (string) $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) $subject['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?php if ($subjectError !== null): ?>

                            <div class="invalid-feedback">
                                <?= htmlspecialchars(
                                    $formatError($subjectError),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">

                        <label
                            for="academic_session_id"
                            class="form-label"
                        >
                            Academic Session
                        </label>

                        <select
                            id="academic_session_id"
                            name="academic_session_id"
                            class="form-select <?= $sessionError !== null ? 'is-invalid' : '' ?>"
                            required
                        >

                            <option value="">
                                -- Select Session --
                            </option>

                            <?php foreach ($sessions as $academicSession): ?>

                                <?php $id = (int) $academicSession['id']; ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $academicSessionId === (string) $id
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars(
                                        (string) $academicSession['name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?php if ($sessionError !== null): ?>

                            <div class="invalid-feedback">
                                <?= htmlspecialchars(
                                    $formatError($sessionError),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">

                        <label
                            for="term_id"
                            class="form-label"
                        >
                            Term
                        </label>

                        <select
                            id="term_id"
                            name="term_id"
                            class="form-select <?= $termError !== null ? 'is-invalid' : '' ?>"
                            required
                        >

                            <option value="">
                                -- Select Term --
                            </option>

                            <?php foreach ($terms as $term): ?>

                                <?php $id = (int) $term['id']; ?>

                                <option
                                    value="<?= $id ?>"
                                    <?= $termId === (string) $id
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

                        <?php if ($termError !== null): ?>

                            <div class="invalid-feedback">
                                <?= htmlspecialchars(
                                    $formatError($termError),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">

                        <label
                            for="ca_score"
                            class="form-label"
                        >
                            Continuous Assessment
                            <span class="text-muted">
                                (0–30)
                            </span>
                        </label>

                        <input
                            type="number"
                            id="ca_score"
                            name="ca_score"
                            class="form-control <?= $caError !== null ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars(
                                $caScore,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            min="0"
                            max="30"
                            step="1"
                            required
                        >

                        <?php if ($caError !== null): ?>

                            <div class="invalid-feedback">
                                <?= htmlspecialchars(
                                    $formatError($caError),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="col-md-6">

                        <label
                            for="exam_score"
                            class="form-label"
                        >
                            Examination
                            <span class="text-muted">
                                (0–70)
                            </span>
                        </label>

                        <input
                            type="number"
                            id="exam_score"
                            name="exam_score"
                            class="form-control <?= $examError !== null ? 'is-invalid' : '' ?>"
                            value="<?= htmlspecialchars(
                                $examScore,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            min="0"
                            max="70"
                            step="1"
                            required
                        >

                        <?php if ($examError !== null): ?>

                            <div class="invalid-feedback">
                                <?= htmlspecialchars(
                                    $formatError($examError),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="alert alert-light border mt-4">

                    <div class="row text-center">

                        <div class="col-md-4">

                            <div class="small text-muted">
                                CA
                            </div>

                            <div
                                class="fs-4 fw-semibold"
                                id="previewCa"
                            >
                                0
                            </div>

                            <div class="small text-muted">
                                / 30
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="small text-muted">
                                Exam
                            </div>

                            <div
                                class="fs-4 fw-semibold"
                                id="previewExam"
                            >
                                0
                            </div>

                            <div class="small text-muted">
                                / 70
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="small text-muted">
                                Total
                            </div>

                            <div
                                class="fs-4 fw-bold"
                                id="previewTotal"
                            >
                                0
                            </div>

                            <div
                                class="small"
                                id="previewGrade"
                            >
                                Grade: F
                            </div>

                        </div>

                    </div>

                </div>

                <div class="d-flex gap-2 mt-4">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Result
                    </button>

                    <a
                        href="/SchoolERP/public/academic-results"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
(function () {
    const caInput = document.getElementById('ca_score');
    const examInput = document.getElementById('exam_score');

    const previewCa =
        document.getElementById('previewCa');

    const previewExam =
        document.getElementById('previewExam');

    const previewTotal =
        document.getElementById('previewTotal');

    const previewGrade =
        document.getElementById('previewGrade');

    function updatePreview() {

        const ca = Math.max(
            0,
            Math.min(
                30,
                Number(caInput.value) || 0
            )
        );

        const exam = Math.max(
            0,
            Math.min(
                70,
                Number(examInput.value) || 0
            )
        );

        const total = ca + exam;

        let grade = 'F';

        if (total >= 75) {
            grade = 'A';
        } else if (total >= 65) {
            grade = 'B';
        } else if (total >= 55) {
            grade = 'C';
        } else if (total >= 45) {
            grade = 'D';
        } else if (total >= 40) {
            grade = 'E';
        }

        previewCa.textContent = ca;
        previewExam.textContent = exam;
        previewTotal.textContent = total;
        previewGrade.textContent =
            'Grade: ' + grade;
    }

    caInput.addEventListener(
        'input',
        updatePreview
    );

    examInput.addEventListener(
        'input',
        updatePreview
    );

    updatePreview();
})();
</script>