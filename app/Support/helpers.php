<?php

declare(strict_types=1);

use SchoolERP\Security\Csrf;

if (!function_exists('csrf')) {

    /**
     * Resolve the CSRF service.
     */
    function csrf(): Csrf
    {
        global $container;

        return $container->make(
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