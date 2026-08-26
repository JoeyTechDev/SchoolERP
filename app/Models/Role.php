<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Role extends Model
{
    /**
     * Database table.
     */
    protected string $table = 'roles';

    /**
     * Mass assignable attributes.
     *
     * @var array<int,string>
     */
    protected array $fillable = [
        'role_name',
        'description',
    ];

    /**
     * Role has many users.
     */
    public function users()
    {
        return $this->hasMany(
            User::class,
            'role_id'
        );
    }
}