<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

/**
 * Handles local and global query scopes.
 */
trait HasScopes
{
    /**
     * Registered global scopes.
     *
     * @var array<class-string, array<int, callable>>
     */
    protected static array $globalScopes = [];

    /**
     * Register a global scope.
     */
    public static function addGlobalScope(
        callable $scope
    ): void {
        static::$globalScopes[static::class][] = $scope;
    }

    /**
     * Apply global scopes for the current model.
     */
    protected function applyGlobalScopes(): void
    {
        foreach (
            static::$globalScopes[static::class] ?? []
            as $scope
        ) {
            $scope($this);
        }
    }
}