<?php

declare(strict_types=1);
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1">
                Create Student
            </h1>

            <p class="text-muted mb-0">
                Add a new student to the school system.
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

            <form
                method="POST"
                action="/SchoolERP/public/students"
            >

                <?= csrf_field() ?>

                <div class="mb-3">

                    <label
                        for="first_name"
                        class="form-label"
                    >
                        First Name
                    </label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label
                        for="last_name"
                        class="form-label"
                    >
                        Last Name
                    </label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label
                        for="classroom_id"
                        class="form-label"
                    >
                        Classroom ID
                    </label>

                    <input
                        type="number"
                        id="classroom_id"
                        name="classroom_id"
                        class="form-control"
                        min="1"
                    >

                    <div class="form-text">
                        Enter the ID of the student's classroom.
                    </div>

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Create Student
                    </button>

                    <a
                        href="/SchoolERP/public/students"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>