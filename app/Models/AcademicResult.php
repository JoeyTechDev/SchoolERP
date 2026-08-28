<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class AcademicResult extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'academic_results';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'student_id',
        'subject_id',
        'academic_session_id',
        'term_id',
        'ca_score',
        'exam_score',
        'total_score',
        'grade',
        'remark',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'student_id' => 'int',
        'subject_id' => 'int',
        'academic_session_id' => 'int',
        'term_id' => 'int',
        'ca_score' => 'int',
        'exam_score' => 'int',
        'total_score' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    /**
     * Result belongs to one subject.
     */
    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id'
        );
    }

    /**
     * Result belongs to one academic session.
     */
    public function academicSession()
    {
        return $this->belongsTo(
            AcademicSession::class,
            'academic_session_id'
        );
    }

    /**
     * Result belongs to one term.
     */
    public function term()
    {
        return $this->belongsTo(
            Term::class,
            'term_id'
        );
    }
}