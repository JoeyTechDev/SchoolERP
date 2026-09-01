<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Teacher extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'teachers';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'employment_status',
        'date_employed',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'user_id' => 'int',
        'date_of_birth' => 'date',
        'date_employed' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Teacher assignments.
     */
    public function assignments()
    {
        return $this->hasMany(
            TeacherAssignment::class,
            'teacher_id'
        );
    }

    /**
     * Teacher belongs to a user account.
     *
     * This relationship is optional because a teacher profile
     * does not have to have a portal account yet.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
