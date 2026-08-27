<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $subjects
 */

$subjects = $subjects ?? [];

?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Subjects
            </h1>

            <p class="text-muted mb-0">
                Manage subjects available throughout the school.
            </p>
        </div>

        <a
            href="/SchoolERP/public/subjects/create"
            class="btn btn-primary"
        >
            + Add Subject
        </a>

    </div>

    <?php if ($subjects === []): ?>

        <div class="alert alert-info">
            No subjects have been created yet.
        </div>

    <?php else: ?>

        <div class="card shadow-sm">

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Subject
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($subjects as $subject): ?>

                                <?php
                                $status = (string) (
                                    $subject['status'] ?? 'active'
                                );

                                $subjectId = (int) (
                                    $subject['id'] ?? 0
                                );
                                ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) $subjectId,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                (string) $subject['name'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>

                                        <?php if (
                                            !empty($subject['description'])
                                        ): ?>

                                            <div class="text-muted small">
                                                <?= htmlspecialchars(
                                                    (string) $subject['description'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </div>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) (
                                                $subject['code']
                                                ?? '—'
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>

                                        <?php if ($status === 'active'): ?>

                                            <span class="badge text-bg-success">
                                                Active
                                            </span>

                                        <?php else: ?>

                                            <span class="badge text-bg-secondary">
                                                Inactive
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-end">

                                        <a
                                            href="/SchoolERP/public/subjects/<?= $subjectId ?>/edit"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <?php if ($status === 'active'): ?>

                                            <form
                                                method="POST"
                                                action="/SchoolERP/public/subjects/<?= $subjectId ?>/deactivate"
                                                class="d-inline"
                                            >

                                                <?= csrf_field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-warning"
                                                >
                                                    Deactivate
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <form
                                                method="POST"
                                                action="/SchoolERP/public/subjects/<?= $subjectId ?>/activate"
                                                class="d-inline"
                                            >

                                                <?= csrf_field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                >
                                                    Activate
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>