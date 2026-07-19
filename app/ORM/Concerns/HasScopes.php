<?php

declare(strict_types=1);

namespace SchoolERP\ORM\Concerns;

use BadMethodCallException;

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
     * Apply global scopes.
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

    /**
     * Execute a local scope.
     */
    public function scope(
        string $scope,
        mixed ...$arguments
    ): static {

        $method = 'scope' . ucfirst($scope);

        if (!method_exists($this, $method)) {
            throw new BadMethodCallException(
                "Scope [{$scope}] does not exist."
            );
        }

        $this->{$method}(...$arguments);

        return $this;
    }
}