<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Contracts;

use SchoolERP\Query\QueryBuilder;

interface GlobalScope
{
    public function apply(QueryBuilder $query): void;
}