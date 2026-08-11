<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Classroom extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'classrooms';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'name',
    ];

    /**
     * Classroom has many students.
     */
    public function students()
    {
        return $this->hasMany(
            Student::class,
            'classroom_id'
        );
    }
}
