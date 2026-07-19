<?php

declare(strict_types=1);

namespace SchoolERP\Models;

use SchoolERP\ORM\Model;

final class Student extends Model
{
    protected string $table = 'students';
    protected array $fillable = [
    'first_name',
    'last_name',
    ];

    protected array $casts = [

    'id' => 'int',

];
}