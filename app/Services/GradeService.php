<?php

declare(strict_types=1);

namespace SchoolERP\Services;

final class GradeService
{
    /**
     * Determine the grade from a total score.
     */
    public function grade(
        int $totalScore
    ): string {
        return match (true) {
            $totalScore >= 75 => 'A',
            $totalScore >= 65 => 'B',
            $totalScore >= 55 => 'C',
            $totalScore >= 45 => 'D',
            $totalScore >= 40 => 'E',
            default => 'F',
        };
    }

    /**
     * Determine the remark from a total score.
     */
    public function remark(
        int $totalScore
    ): string {
        return match (true) {
            $totalScore >= 75 => 'Excellent',
            $totalScore >= 65 => 'Very Good',
            $totalScore >= 55 => 'Good',
            $totalScore >= 45 => 'Fair',
            $totalScore >= 40 => 'Pass',
            default => 'Fail',
        };
    }
}