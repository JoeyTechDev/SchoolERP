<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Classroom extends Model
{
    protected string $table = 'classrooms';

    public function students()
    {
        return $this->hasMany(
            Student::class,
            'classroom_id'
        );
    }
}