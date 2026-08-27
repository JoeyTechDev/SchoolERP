<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class AcademicSession extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'academic_sessions';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'name',
        'is_current',
        'status',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'id' => 'int',
        'is_current' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}