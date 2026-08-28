<?php

declare(strict_types=1);

namespace SchoolERP\Services;

use InvalidArgumentException;

final class ResultCalculationService
{
    /**
     * Maximum CA score.
     */
    public const MAX_CA_SCORE = 30;

    /**
     * Maximum examination score.
     */
    public const MAX_EXAM_SCORE = 70;

    /**
     * Maximum total score.
     */
    public const MAX_TOTAL_SCORE = 100;

    /**
     * Calculate total score.
     *
     * @throws InvalidArgumentException
     */
    public function total(
        int $caScore,
        int $examScore
    ): int {
        $this->validate(
            $caScore,
            $examScore
        );

        return $caScore + $examScore;
    }

    /**
     * Validate CA score.
     *
     * @throws InvalidArgumentException
     */
    public function validateCaScore(
        int $score
    ): void {
        if (
            $score < 0
            || $score > self::MAX_CA_SCORE
        ) {
            throw new InvalidArgumentException(
                'CA score must be between 0 and 30.'
            );
        }
    }

    /**
     * Validate examination score.
     *
     * @throws InvalidArgumentException
     */
    public function validateExamScore(
        int $score
    ): void {
        if (
            $score < 0
            || $score > self::MAX_EXAM_SCORE
        ) {
            throw new InvalidArgumentException(
                'Exam score must be between 0 and 70.'
            );
        }
    }

    /**
     * Validate both assessment scores.
     *
     * @throws InvalidArgumentException
     */
    public function validate(
        int $caScore,
        int $examScore
    ): void {
        $this->validateCaScore(
            $caScore
        );

        $this->validateExamScore(
            $examScore
        );
    }
}