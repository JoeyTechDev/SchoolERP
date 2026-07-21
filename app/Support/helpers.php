<?php

declare(strict_types=1);

use SchoolERP\View\ViewFactory;

if (!function_exists('view')) {

    /**
     * Render a view.
     *
     * @param array<string,mixed> $data
     */
    function view(
        string $view,
        array $data = []
    ): string {

        static $factory = null;

        if ($factory === null) {

            $factory = new ViewFactory(
                dirname(__DIR__) . '/Views'
            );
        }

        return $factory
            ->make($view, $data)
            ->render();
    }
}