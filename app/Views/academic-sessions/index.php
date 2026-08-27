<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $sessions
 * @var \SchoolERP\Models\AcademicSession|null $current
 */

$sessions = $sessions ?? [];
$current = $current ?? null;
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Academic Sessions
            </h1>

            <p class="text-muted mb-0">
                Manage school academic years and select the current session.
            </p>
        </div>

        <a
            href="/SchoolERP/public/academic-sessions/create"
            class="btn btn-primary"
        >
            + Add Session
        </a>

    </div>

    <?php if ($sessions === []): ?>

        <div class="alert alert-info">
            No academic sessions have been created yet.
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
                                    Academic Session
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Current
                                </th>

                                <th class="text-end">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($sessions as $session): ?>

                                <?php
                                $sessionId = (int) (
                                    $session['id'] ?? 0
                                );

                                $name = (string) (
                                    $session['name'] ?? ''
                                );

                                $status = (string) (
                                    $session['status'] ?? 'inactive'
                                );

                                $isCurrent = (int) (
                                    $session['is_current'] ?? 0
                                ) === 1;
                                ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) $sessionId,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars(
                                                $name,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </strong>
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

                                    <td>

                                        <?php if ($isCurrent): ?>

                                            <span class="badge text-bg-primary">
                                                Current
                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-end">

                                        <a
                                            href="/SchoolERP/public/academic-sessions/<?= $sessionId ?>/edit"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <?php if (!$isCurrent && $status === 'active'): ?>

                                            <form
                                                method="POST"
                                                action="/SchoolERP/public/academic-sessions/<?= $sessionId ?>/current"
                                                class="d-inline"
                                            >

                                                <?= csrf_field() ?>

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                >
                                                    Set Current
                                                </button>

                                            </form>

                                            <form
                                                method="POST"
                                                action="/SchoolERP/public/academic-sessions/<?= $sessionId ?>/deactivate"
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

                                        <?php elseif (!$isCurrent): ?>

                                            <form
                                                method="POST"
                                                action="/SchoolERP/public/academic-sessions/<?= $sessionId ?>/activate"
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