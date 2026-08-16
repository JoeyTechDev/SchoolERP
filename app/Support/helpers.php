<?php

declare(strict_types=1);

use SchoolERP\Security\Csrf;
use SchoolERP\Session\SessionInterface;

if (!function_exists('container')) {

    /**
     * Get the application container.
     */
    function container(): mixed
    {
        global $container;

        return $container;
    }
}

if (!function_exists('session')) {

    /**
     * Resolve the session service.
     */
    function session(): SessionInterface
    {
        return container()->make(
            SessionInterface::class
        );
    }
}

if (!function_exists('csrf')) {

    /**
     * Resolve the CSRF service.
     */
    function csrf(): Csrf
    {
        return container()->make(
            Csrf::class
        );
    }
}

if (!function_exists('csrf_token')) {

    /**
     * Get the current CSRF token.
     */
    function csrf_token(): string
    {
        return csrf()->token();
    }
}

if (!function_exists('csrf_field')) {

    /**
     * Generate a hidden CSRF field.
     */
    function csrf_field(): string
    {
        return sprintf(
            '<input type="hidden" name="_token" value="%s">',
            htmlspecialchars(
                csrf_token(),
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }
}

/*
|--------------------------------------------------------------------------
| Old Input
|--------------------------------------------------------------------------
*/

if (!function_exists('old')) {

    /**
     * Retrieve previously submitted input.
     */
    function old(
        string $key,
        mixed $default = null
    ): mixed {

        static $oldInput = null;

        if ($oldInput === null) {

            /*
             * IMPORTANT:
             * Call flash() with ONE argument so that it retrieves
             * the flash value instead of storing a new value.
             */
            $oldInput = session()->flash(
                '_old_input'
            );

            if (!is_array($oldInput)) {
                $oldInput = [];
            }
        }

        return $oldInput[$key] ?? $default;
    }
}

/*
|--------------------------------------------------------------------------
| Validation Errors
|--------------------------------------------------------------------------
*/

if (!function_exists('validation_errors')) {

    /**
     * Retrieve all validation errors.
     *
     * @return array<string,array<int,string>>
     */
    function validation_errors(): array
    {
        static $errors = null;

        if ($errors === null) {

            /*
             * IMPORTANT:
             * Do NOT pass a second argument.
             *
             * SessionManager::flash() uses the number of arguments
             * to distinguish reading from writing.
             */
            $errors = session()->flash(
                '_errors'
            );

            if (!is_array($errors)) {
                $errors = [];
            }
        }

        return $errors;
    }
}

if (!function_exists('has_errors')) {

    /**
     * Determine whether validation errors exist.
     */
    function has_errors(): bool
    {
        return validation_errors() !== [];
    }
}

if (!function_exists('has_error')) {

    /**
     * Determine whether a field has validation errors.
     */
    function has_error(
        string $field
    ): bool {

        $errors = validation_errors();

        return isset($errors[$field])
            && $errors[$field] !== [];
    }
}

if (!function_exists('first_error')) {

    /**
     * Get the first validation error for a field.
     */
    function first_error(
        string $field,
        string $default = ''
    ): string {

        $errors = validation_errors();

        if (
            !isset($errors[$field])
            || $errors[$field] === []
        ) {
            return $default;
        }

        return (string) $errors[$field][0];
    }
}

/**
 * Get a flash message from the session.
 *
 * Flash messages are automatically removed after retrieval.
 */
if (!function_exists('flash')) {

    function flash(
        string $key,
        mixed $default = null
    ): mixed {

        $container = $GLOBALS['container'] ?? null;

        if ($container === null) {
            return $default;
        }

        $session = $container->make(
            \SchoolERP\Session\SessionInterface::class
        );

        return $session->flash(
            $key
        ) ?? $default;
    }
}