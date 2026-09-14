<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;
use SchoolERP\Models\User;

final class Student extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'students';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'classroom_id',
        'user_id',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'classroom_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Student belongs to one login user account.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Student belongs to one classroom.
     */
    public function classroom()
    {
        return $this->belongsTo(
            Classroom::class,
            'classroom_id'
        );
    }

    /**
     * Scope students in a classroom.
     */
    public function scopeInClassroom(
        int $classroomId
    ): static {
        return $this->where(
            'classroom_id',
            '=',
            $classroomId
        );
    }

    /**
     * Student has many results.
     */
    public function results()
    {
        return $this->hasMany(
            Result::class,
            'student_id'
        );
    }
}
