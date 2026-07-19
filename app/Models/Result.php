<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Result extends Model
{
    protected string $table = 'results';

    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }
}