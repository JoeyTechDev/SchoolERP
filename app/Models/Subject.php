<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Subject extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'subjects';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'name',
        'code',
        'status',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Subject has many teacher assignments.
     */
    public function teacherAssignments()
    {
        return $this->hasMany(
            TeacherAssignment::class,
            'subject_id'
        );
    }
}
