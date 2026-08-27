<?php

declare(strict_types=1);

/**
 * @var array<int,array<string,mixed>> $terms
 */

$terms = $terms ?? [];
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h1 class="h3 mb-1">
                Academic Terms
            </h1>

            <p class="text-muted mb-0">
                Manage the terms used in academic records.
            </p>

        </div>

        <a
            href="/SchoolERP/public/terms/create"
            class="btn btn-primary"
        >
            + Add Term
        </a>

    </div>

    <?php if ($terms === []): ?>

        <div class="alert alert-info">
            No terms have been created yet.
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
                                    Term
                                </th>

                                <th>
                                    Order
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

                            <?php foreach ($terms as $term): ?>

                                <?php
                                $termId = (int) (
                                    $term['id'] ?? 0
                                );

                                $name = (string) (
                                    $term['name'] ?? ''
                                );

                                $sortOrder = (int) (
                                    $term['sort_order'] ?? 0
                                );

                                $status = (string) (
                                    $term['status'] ?? 'inactive'
                                );
                                ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) $termId,
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
                                        <?= htmlspecialchars(
                                            (string) $sortOrder,
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
                                            href="/SchoolERP/public/terms/<?= $termId ?>/edit"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            Edit
                                        </a>

                                        <?php if ($status === 'active'): ?>

                                            <form
                                                method="POST"
                                                action="/SchoolERP/public/terms/<?= $termId ?>/deactivate"
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
                                                action="/SchoolERP/public/terms/<?= $termId ?>/activate"
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