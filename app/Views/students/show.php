<?php

declare(strict_types=1);
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Student Details</h1>
            <p class="text-muted mb-0">
                View student information.
            </p>
        </div>

        <a
            href="/SchoolERP/public/students"
            class="btn btn-secondary"
        >
            Back to Students
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    ID
                </div>

                <div class="col-md-8">
                    <?= htmlspecialchars(
                        (string) $student->id,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    First Name
                </div>

                <div class="col-md-8">
                    <?= htmlspecialchars(
                        (string) $student->first_name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Last Name
                </div>

                <div class="col-md-8">
                    <?= htmlspecialchars(
                        (string) $student->last_name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">
                    Classroom ID
                </div>

                <div class="col-md-8">
                    <?= htmlspecialchars(
                        (string) $student->classroom_id,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            </div>

            <?php if ($student->created_at !== null): ?>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">
                        Created At
                    </div>

                    <div class="col-md-8">
                        <?= htmlspecialchars(
                            $student->created_at->format(
                                'Y-m-d H:i:s'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($student->updated_at !== null): ?>
                <div class="row">
                    <div class="col-md-4 fw-bold">
                        Updated At
                    </div>

                    <div class="col-md-8">
                        <?= htmlspecialchars(
                            $student->updated_at->format(
                                'Y-m-d H:i:s'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>