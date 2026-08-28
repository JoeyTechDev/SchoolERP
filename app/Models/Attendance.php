<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Attendance extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'attendance';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'student_id',
        'academic_session_id',
        'term_id',
        'attendance_date',
        'status',
        'remarks',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'student_id' => 'int',
        'academic_session_id' => 'int',
        'term_id' => 'int',
        'attendance_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Attendance belongs to one student.
     */
    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }

    /**
     * Attendance belongs to one academic session.
     */
    public function academicSession()
    {
        return $this->belongsTo(
            AcademicSession::class,
            'academic_session_id'
        );
    }

    /**
     * Attendance belongs to one term.
     */
    public function term()
    {
        return $this->belongsTo(
            Term::class,
            'term_id'
        );
    }
}