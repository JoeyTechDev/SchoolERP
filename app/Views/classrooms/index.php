<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $classrooms
 */

$classrooms = $classrooms ?? [];

?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Classrooms
            </h1>

            <p class="text-muted mb-0">
                Manage school classrooms and student assignments.
            </p>
        </div>

        <a
            href="/SchoolERP/public/classrooms/create"
            class="btn btn-primary"
        >
            + Add Classroom
        </a>

    </div>

    <?php if ($classrooms === []): ?>

        <div class="alert alert-info">
            No classrooms have been created yet.
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
                                    Classroom
                                </th>

                                <th>
                                    Students
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($classrooms as $classroom): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) $classroom['id'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                (string) $classroom['name'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) (
                                                $classroom['student_count']
                                                ?? 0
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td class="text-end">

                                        <a
                                            href="/SchoolERP/public/classrooms/<?= htmlspecialchars(
                                                (string) $classroom['id'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>/edit"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            action="/SchoolERP/public/classrooms/<?= htmlspecialchars(
                                                (string) $classroom['id'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>/delete"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this classroom?');"
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

            </div>

        </div>

    <?php endif; ?>

</div>
