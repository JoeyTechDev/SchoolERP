<?php

declare(strict_types=1);

/**
 * @var array<string,mixed> $report
 */

$report = $report ?? [];

/*
|--------------------------------------------------------------------------
| School Configuration
|--------------------------------------------------------------------------
*/

$school = require __DIR__ . '/../../../config/school.php';

$schoolName = (string) (
    $school['name'] ?? 'SchoolERP'
);

$schoolMotto = (string) (
    $school['motto'] ?? ''
);

$schoolAddress = trim(
    (string) (
        $school['address'] ?? ''
    )
);

$schoolPhone = trim(
    (string) (
        $school['phone'] ?? ''
    )
);

$schoolEmail = trim(
    (string) (
        $school['email'] ?? ''
    )
);

$schoolLogo = trim(
    (string) (
        $school['logo'] ?? ''
    )
);

/*
|--------------------------------------------------------------------------
| Report Data
|--------------------------------------------------------------------------
*/

$student = $report['student'] ?? null;

$classroom = $report['classroom'] ?? null;

$academicSession =
    $report['academic_session'] ?? null;

$term = $report['term'] ?? null;

$results = $report['results'] ?? [];

$attendanceSummary =
    $report['attendance_summary'] ?? null;

$reportSummary =
    $report['report_summary'] ?? null;

$totalScore = (int) (
    $report['total_score'] ?? 0
);

$averageScore = (float) (
    $report['average_score'] ?? 0
);

$resultCount = (int) (
    $report['result_count'] ?? 0
);

$position = $report['position'] ?? null;

$rankedStudents = (int) (
    $report['ranked_students'] ?? 0
);

/*
|--------------------------------------------------------------------------
| Remarks
|--------------------------------------------------------------------------
*/

$classTeacherRemark = '';

$principalRemark = '';

$promotionStatus = 'pending';

if ($reportSummary !== null) {

    $classTeacherRemark = (string) (
        $reportSummary->class_teacher_remark
        ?? ''
    );

    $principalRemark = (string) (
        $reportSummary->principal_remark
        ?? ''
    );

    $promotionStatus = (string) (
        $reportSummary->promotion_status
        ?? 'pending'
    );
}

/*
|--------------------------------------------------------------------------
| Student Name
|--------------------------------------------------------------------------
*/

$studentName = '';

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
}

/*
|--------------------------------------------------------------------------
| Position Formatting
|--------------------------------------------------------------------------
*/

$ordinal = static function (
    ?int $value
): string {

    if ($value === null) {
        return '—';
    }

    $mod100 = $value % 100;

    if (
        $mod100 >= 11
        && $mod100 <= 13
    ) {
        return $value . 'th';
    }

    return match ($value % 10) {

        1 => $value . 'st',

        2 => $value . 'nd',

        3 => $value . 'rd',

        default => $value . 'th',

    };
};

/*
|--------------------------------------------------------------------------
| Promotion Label
|--------------------------------------------------------------------------
*/

$promotionLabel = match (
    $promotionStatus
) {

    'promoted' =>
        'Promoted',

    'not_promoted' =>
        'Not Promoted',

    default =>
        'Pending',

};
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars(
            $schoolName,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
        | Report Card
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            padding: 30px;

            background: #eef1f5;

            color: #111827;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            line-height: 1.5;
        }

        .report {

            width: 100%;

            max-width: 900px;

            margin: 0 auto;

            background: #ffffff;

            padding: 38px;

            box-shadow:
                0 4px 18px
                rgba(0, 0, 0, 0.08);
        }

        /*
        |--------------------------------------------------------------------------
        | School Header
        |--------------------------------------------------------------------------
        */

        .school-header {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 18px;

            text-align: center;

            padding-bottom: 18px;

            border-bottom: 2px solid #111827;

            margin-bottom: 20px;
        }

        .school-logo {

            width: 78px;

            height: 78px;

            object-fit: contain;

            flex-shrink: 0;
        }

        .school-brand {

            min-width: 0;
        }

        .school-name {

            margin: 0;

            font-size: 28px;

            font-weight: 700;

            letter-spacing: 0.5px;
        }

        .school-motto {

            margin: 3px 0 0;

            font-size: 13px;

            color: #4b5563;
        }

        .school-contact {

            margin-top: 6px;

            font-size: 11px;

            color: #6b7280;

            line-height: 1.5;
        }

        .report-title {

            margin: 5px 0 0;

            font-size: 15px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 0.7px;
        }

        /*
        |--------------------------------------------------------------------------
        | Student Header
        |--------------------------------------------------------------------------
        */

        .student-header {

            display: grid;

            grid-template-columns:
                2fr 1fr 1fr;

            gap: 12px;

            margin-bottom: 12px;
        }

        .info-box {

            border: 1px solid #d1d5db;

            padding: 11px;

            min-height: 59px;
        }

        .info-label {

            display: block;

            font-size: 10px;

            color: #6b7280;

            text-transform: uppercase;

            letter-spacing: 0.5px;

            margin-bottom: 3px;
        }

        .info-value {

            font-weight: 600;

            font-size: 13px;
        }

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        .section-title {

            margin: 24px 0 10px;

            padding-bottom: 6px;

            border-bottom: 1px solid #9ca3af;

            font-size: 16px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 0.3px;
        }

        /*
        |--------------------------------------------------------------------------
        | Academic Table
        |--------------------------------------------------------------------------
        */

        table {

            width: 100%;

            border-collapse: collapse;
        }

        th,
        td {

            border: 1px solid #9ca3af;

            padding: 7px 8px;

            font-size: 11px;
        }

        th {

            background: #f3f4f6;

            font-weight: 700;

            text-align: left;
        }

        .text-center {

            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        .summary-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 8px;

            margin-top: 12px;
        }

        .summary-box {

            border: 1px solid #d1d5db;

            padding: 10px;

            text-align: center;
        }

        .summary-label {

            font-size: 9px;

            color: #6b7280;

            text-transform: uppercase;
        }

        .summary-value {

            margin-top: 3px;

            font-size: 18px;

            font-weight: 700;
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        .attendance-grid {

            display: grid;

            grid-template-columns:
                repeat(6, 1fr);

            gap: 7px;

            margin-top: 10px;
        }

        .attendance-box {

            border: 1px solid #d1d5db;

            padding: 9px 5px;

            text-align: center;
        }

        .attendance-label {

            display: block;

            font-size: 9px;

            color: #6b7280;

            text-transform: uppercase;
        }

        .attendance-value {

            display: block;

            margin-top: 3px;

            font-size: 16px;

            font-weight: 700;
        }

        /*
        |--------------------------------------------------------------------------
        | Remarks
        |--------------------------------------------------------------------------
        */

        .remarks-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 12px;
        }

        .remark-box {

            border: 1px solid #d1d5db;

            min-height: 105px;

            padding: 12px;
        }

        .remark-title {

            margin-bottom: 7px;

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 0.3px;
        }

        .remark-text {

            font-size: 12px;

            white-space: pre-line;
        }

        /*
        |--------------------------------------------------------------------------
        | Promotion
        |--------------------------------------------------------------------------
        */

        .promotion {

            margin-top: 14px;

            border: 1px solid #9ca3af;

            padding: 11px;

            text-align: center;
        }

        .promotion-label {

            font-size: 9px;

            color: #6b7280;

            text-transform: uppercase;

            letter-spacing: 0.4px;
        }

        .promotion-value {

            margin-top: 3px;

            font-size: 17px;

            font-weight: 700;

            text-transform: uppercase;
        }

        /*
        |--------------------------------------------------------------------------
        | Signatures
        |--------------------------------------------------------------------------
        */

        .signature-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 70px;

            margin-top: 65px;
        }

        .signature {

            border-top: 1px solid #111827;

            padding-top: 7px;

            font-size: 11px;
        }

        /*
        |--------------------------------------------------------------------------
        | Footer
        |--------------------------------------------------------------------------
        */

        .footer {

            margin-top: 30px;

            padding-top: 10px;

            border-top: 1px solid #d1d5db;

            text-align: center;

            color: #6b7280;

            font-size: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | Empty
        |--------------------------------------------------------------------------
        */

        .empty {

            text-align: center;

            color: #6b7280;

            padding: 18px;
        }

        /*
        |--------------------------------------------------------------------------
        | Print
        |--------------------------------------------------------------------------
        */

        @media print {

            body {

                padding: 0;

                background: #ffffff;
            }

            .report {

                max-width: none;

                padding: 0;

                box-shadow: none;
            }

            @page {

                size: A4 portrait;

                margin: 10mm;
            }

        }

        /*
        |--------------------------------------------------------------------------
        | Mobile
        |--------------------------------------------------------------------------
        */

        @media (max-width: 700px) {

            body {

                padding: 10px;
            }

            .report {

                padding: 20px;
            }

            .school-header {

                flex-direction: column;

                gap: 8px;
            }

            .student-header {

                grid-template-columns: 1fr;
            }

            .summary-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }

            .attendance-grid {

                grid-template-columns:
                    repeat(3, 1fr);
            }

            .remarks-grid,
            .signature-grid {

                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="report">

    <!-- ============================================================= -->
    <!-- SCHOOL HEADER                                                  -->
    <!-- ============================================================= -->

    <header class="school-header">

        <?php if ($schoolLogo !== ''): ?>

            <img
                src="<?= htmlspecialchars(
                    $schoolLogo,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                alt="School Logo"
                class="school-logo"
            >

        <?php endif; ?>

        <div class="school-brand">

            <h1 class="school-name">

                <?= htmlspecialchars(
                    $schoolName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </h1>

            <?php if ($schoolMotto !== ''): ?>

                <div class="school-motto">

                    <?= htmlspecialchars(
                        $schoolMotto,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>

            <?php if (
                $schoolAddress !== ''
                || $schoolPhone !== ''
                || $schoolEmail !== ''
            ): ?>

                <div class="school-contact">

                    <?php if (
                        $schoolAddress !== ''
                    ): ?>

                        <?= htmlspecialchars(
                            $schoolAddress,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    <?php endif; ?>

                    <?php if (
                        $schoolPhone !== ''
                    ): ?>

                        <?php if (
                            $schoolAddress !== ''
                        ): ?>

                            &nbsp; | &nbsp;

                        <?php endif; ?>

                        <?= htmlspecialchars(
                            $schoolPhone,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    <?php endif; ?>

                    <?php if (
                        $schoolEmail !== ''
                    ): ?>

                        <?php if (
                            $schoolAddress !== ''
                            || $schoolPhone !== ''
                        ): ?>

                            &nbsp; | &nbsp;

                        <?php endif; ?>

                        <?= htmlspecialchars(
                            $schoolEmail,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

            <div class="report-title">

                Student Academic Report Card

            </div>

        </div>

    </header>


    <!-- ============================================================= -->
    <!-- STUDENT INFORMATION                                            -->
    <!-- ============================================================= -->

    <div class="student-header">

        <div class="info-box">

            <span class="info-label">
                Student
            </span>

            <span class="info-value">

                <?= htmlspecialchars(
                    $studentName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>

        </div>


        <div class="info-box">

            <span class="info-label">
                Classroom
            </span>

            <span class="info-value">

                <?= htmlspecialchars(
                    $classroom !== null
                        ? (string) (
                            $classroom->name ?? ''
                        )
                        : 'Not Assigned',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>

        </div>


        <div class="info-box">

            <span class="info-label">
                Academic Session
            </span>

            <span class="info-value">

                <?= htmlspecialchars(
                    (string) (
                        $academicSession->name ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>

        </div>

    </div>


    <div class="student-header">

        <div class="info-box">

            <span class="info-label">
                Term
            </span>

            <span class="info-value">

                <?= htmlspecialchars(
                    (string) (
                        $term->name ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>

        </div>


        <div class="info-box">

            <span class="info-label">
                Subjects
            </span>

            <span class="info-value">
                <?= $resultCount ?>
            </span>

        </div>


        <div class="info-box">

            <span class="info-label">
                Class Position
            </span>

            <span class="info-value">

                <?= htmlspecialchars(
                    $ordinal($position),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

                <?php if (
                    $rankedStudents > 0
                ): ?>

                    of <?= $rankedStudents ?>

                <?php endif; ?>

            </span>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- ACADEMIC PERFORMANCE                                          -->
    <!-- ============================================================= -->

    <div class="section-title">
        Academic Performance
    </div>


    <?php if ($results === []): ?>

        <div class="empty">
            No academic results recorded.
        </div>

    <?php else: ?>

        <table>

            <thead>

                <tr>

                    <th
                        class="text-center"
                        style="width: 35px;"
                    >
                        #
                    </th>

                    <th>
                        Subject
                    </th>

                    <th class="text-center">
                        CA / 30
                    </th>

                    <th class="text-center">
                        Exam / 70
                    </th>

                    <th class="text-center">
                        Total / 100
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

                <?php $counter = 1; ?>

                <?php foreach (
                    $results
                    as $result
                ): ?>

                    <tr>

                        <td class="text-center">
                            <?= $counter++ ?>
                        </td>

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    (string) (
                                        $result[
                                            'subject_name'
                                        ]
                                        ?? 'Unknown Subject'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </strong>

                            <?php if (
                                !empty(
                                    $result[
                                        'subject_code'
                                    ]
                                )
                            ): ?>

                                <br>

                                <small>

                                    <?= htmlspecialchars(
                                        (string) (
                                            $result[
                                                'subject_code'
                                            ]
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </small>

                            <?php endif; ?>

                        </td>

                        <td class="text-center">

                            <?= htmlspecialchars(
                                (string) (
                                    $result[
                                        'ca_score'
                                    ]
                                    ?? '—'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>

                        <td class="text-center">

                            <?= htmlspecialchars(
                                (string) (
                                    $result[
                                        'exam_score'
                                    ]
                                    ?? '—'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>

                        <td class="text-center">

                            <strong>

                                <?= htmlspecialchars(
                                    (string) (
                                        $result[
                                            'total_score'
                                        ]
                                        ?? '—'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </strong>

                        </td>

                        <td class="text-center">

                            <?= htmlspecialchars(
                                (string) (
                                    $result[
                                        'grade'
                                    ]
                                    ?? '—'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                (
                                    (string) (
                                        $result[
                                            'remark'
                                        ] ?? ''
                                    )
                                ) !== ''
                                    ? (string) (
                                        $result[
                                            'remark'
                                        ]
                                    )
                                    : '—',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>


    <!-- ============================================================= -->
    <!-- ACADEMIC SUMMARY                                               -->
    <!-- ============================================================= -->

    <div class="section-title">
        Academic Summary
    </div>

    <div class="summary-grid">

        <div class="summary-box">

            <div class="summary-label">
                Subjects
            </div>

            <div class="summary-value">
                <?= $resultCount ?>
            </div>

        </div>


        <div class="summary-box">

            <div class="summary-label">
                Total Score
            </div>

            <div class="summary-value">
                <?= $totalScore ?>
            </div>

        </div>


        <div class="summary-box">

            <div class="summary-label">
                Average
            </div>

            <div class="summary-value">

                <?= number_format(
                    $averageScore,
                    2
                ) ?>

            </div>

        </div>


        <div class="summary-box">

            <div class="summary-label">
                Position
            </div>

            <div class="summary-value">

                <?= htmlspecialchars(
                    $ordinal($position),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- ATTENDANCE                                                     -->
    <!-- ============================================================= -->

    <?php if (
        $attendanceSummary !== null
    ): ?>

        <div class="section-title">
            Attendance
        </div>

        <div class="attendance-grid">

            <div class="attendance-box">

                <span class="attendance-label">
                    School Days
                </span>

                <span class="attendance-value">
                    <?= (int) (
                        $attendanceSummary[
                            'total_days'
                        ] ?? 0
                    ) ?>
                </span>

            </div>


            <div class="attendance-box">

                <span class="attendance-label">
                    Present
                </span>

                <span class="attendance-value">
                    <?= (int) (
                        $attendanceSummary[
                            'present'
                        ] ?? 0
                    ) ?>
                </span>

            </div>


            <div class="attendance-box">

                <span class="attendance-label">
                    Absent
                </span>

                <span class="attendance-value">
                    <?= (int) (
                        $attendanceSummary[
                            'absent'
                        ] ?? 0
                    ) ?>
                </span>

            </div>


            <div class="attendance-box">

                <span class="attendance-label">
                    Late
                </span>

                <span class="attendance-value">
                    <?= (int) (
                        $attendanceSummary[
                            'late'
                        ] ?? 0
                    ) ?>
                </span>

            </div>


            <div class="attendance-box">

                <span class="attendance-label">
                    Excused
                </span>

                <span class="attendance-value">
                    <?= (int) (
                        $attendanceSummary[
                            'excused'
                        ] ?? 0
                    ) ?>
                </span>

            </div>


            <div class="attendance-box">

                <span class="attendance-label">
                    Rate
                </span>

                <span class="attendance-value">

                    <?= number_format(
                        (float) (
                            $attendanceSummary[
                                'attendance_rate'
                            ] ?? 0
                        ),
                        2
                    ) ?>%

                </span>

            </div>

        </div>

    <?php endif; ?>


    <!-- ============================================================= -->
    <!-- REMARKS                                                        -->
    <!-- ============================================================= -->

    <div class="section-title">
        Remarks
    </div>

    <div class="remarks-grid">

        <div class="remark-box">

            <div class="remark-title">
                Class Teacher's Remark
            </div>

            <div class="remark-text">

                <?= htmlspecialchars(
                    $classTeacherRemark !== ''
                        ? $classTeacherRemark
                        : 'No remark recorded.',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="remark-box">

            <div class="remark-title">
                Principal / Administrator's Remark
            </div>

            <div class="remark-text">

                <?= htmlspecialchars(
                    $principalRemark !== ''
                        ? $principalRemark
                        : 'No remark recorded.',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- PROMOTION                                                      -->
    <!-- ============================================================= -->

    <div class="promotion">

        <div class="promotion-label">
            Promotion Status
        </div>

        <div class="promotion-value">

            <?= htmlspecialchars(
                $promotionLabel,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- SIGNATURES                                                      -->
    <!-- ============================================================= -->

    <div class="signature-grid">

        <div class="signature">
            Class Teacher's Signature
        </div>

        <div class="signature">
            Principal's Signature
        </div>

    </div>


    <!-- ============================================================= -->
    <!-- FOOTER                                                         -->
    <!-- ============================================================= -->

    <div class="footer">

        <?= htmlspecialchars(
            $schoolName,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

        &mdash;

        Student Academic Report

    </div>

</div>


<script>
window.addEventListener(
    'load',
    function () {
        window.print();
    }
);
</script>

</body>

</html>