<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Result extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'results';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'student_id',
        'subject',
        'score',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'student_id' => 'int',
        'score' => 'int',
    ];

    /**
     * Result belongs to one student.
     */
    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }
}