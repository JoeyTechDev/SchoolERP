<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class ReportCardSummary extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'report_card_summaries';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'student_id',
        'academic_session_id',
        'term_id',
        'class_teacher_remark',
        'principal_remark',
        'promotion_status',
        'class_teacher_id',
        'principal_id',
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
        'class_teacher_id' => 'int',
        'principal_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Summary belongs to one student.
     */
    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }

    /**
     * Summary belongs to one academic session.
     */
    public function academicSession()
    {
        return $this->belongsTo(
            AcademicSession::class,
            'academic_session_id'
        );
    }

    /**
     * Summary belongs to one term.
     */
    public function term()
    {
        return $this->belongsTo(
            Term::class,
            'term_id'
        );
    }

    /**
     * Summary belongs to the class teacher.
     */
    public function classTeacher()
    {
        return $this->belongsTo(
            User::class,
            'class_teacher_id'
        );
    }

    /**
     * Summary belongs to the principal.
     */
    public function principal()
    {
        return $this->belongsTo(
            User::class,
            'principal_id'
        );
    }
}
