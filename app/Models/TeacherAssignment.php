<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class TeacherAssignment extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'teacher_assignments';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'teacher_id',
        'classroom_id',
        'subject_id',
        'is_active',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'teacher_id' => 'int',
        'classroom_id' => 'int',
        'subject_id' => 'int',
        'is_active' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Assignment belongs to a teacher.
     */
    public function teacher()
    {
        return $this->belongsTo(
            Teacher::class,
            'teacher_id'
        );
    }

    /**
     * Assignment belongs to a classroom.
     */
    public function classroom()
    {
        return $this->belongsTo(
            Classroom::class,
            'classroom_id'
        );
    }

    /**
     * Assignment belongs to a subject.
     */
    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id'
        );
    }
}
