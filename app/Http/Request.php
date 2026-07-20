<?php

declare(strict_types=1);

namespace SchoolERP\Http;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Request
 * --------------------------------------------------------------------------
 *
 * Represents the current HTTP request.
 *
 * Responsibilities
 * ----------------
 * • Capture PHP superglobals
 * • Provide a clean API
 * • Hide global state
 * • Supply request data to the application
 *
 * This class intentionally has no dependencies on:
 *
 * • Router
 * • Session
 * • Database
 * • Authentication
 * • Container
 */
final class Request
{
    /**
     * GET parameters.
     */
    private array $get;

    /**
     * POST parameters.
     */
    private array $post;

    /**
     * Uploaded files.
     */
    private array $files;

    /**
     * Cookies.
     */
    private array $cookies;

    /**
     * Server variables.
     */
    /**
 * Server variables.
 */
private array $server;

/**
 * Current request instance.
 */
private static ?self $instance = null;

/**
 * Private constructor.
 */
private function __construct(
    array $get = [],
    array $post = [],
    array $files = [],
    array $cookies = [],
    array $server = []
) {
    $this->get = $get;
    $this->post = $post;
    $this->files = $files;
    $this->cookies = $cookies;
    $this->server = $server;
}

/**
 * Capture the current HTTP request.
 */
public static function capture(): self
{
    if (self::$instance === null) {
        self::$instance = new self(
            $_GET,
            $_POST,
            $_FILES,
            $_COOKIE,
            $_SERVER
        );
    }

    return self::$instance;
}

/**
 * Get the HTTP request method.
 */
public function method(): string
{
    return strtoupper(
        $this->server['REQUEST_METHOD'] ?? 'GET'
    );
}

/**
 * Get the request URI.
 */
public function uri(): string
{
    return $this->server['REQUEST_URI'] ?? '/';
}

/**
 * Get the request path without query string.
 */
public function path(): string
{
    $path = parse_url(
        $this->uri(),
        PHP_URL_PATH
    );

    if ($path === null || $path === '') {
        return '/';
    }

    /*
     * Remove the application's base directory
     * (e.g. /SchoolERP/public)
     */
    $basePath = str_replace(
        '\\',
        '/',
        dirname($this->server['SCRIPT_NAME'] ?? '')
    );

    if (
        $basePath !== '/'
        && str_starts_with($path, $basePath)
    ) {
        $path = substr(
            $path,
            strlen($basePath)
        );
    }

    return $path === ''
        ? '/'
        : $path;
}

/**
 * Get all GET parameters.
 */
public function query(): array
{
    return $this->get;
}

/**
 * Get all POST parameters.
 */
public function post(): array
{
    return $this->post;
}

/**
 * Get all request input.
 */
public function all(): array
{
    return array_merge(
        $this->get,
        $this->post
    );
}

/**
 * Get an input value from GET or POST.
 */
public function input(
    string $key,
    mixed $default = null
): mixed {

    return $this->post[$key]
        ?? $this->get[$key]
        ?? $default;
}

/**
 * Determine if an input exists.
 */
public function has(string $key): bool
{
    return array_key_exists($key, $this->post)
        || array_key_exists($key, $this->get);
}

/**
 * Determine if an input is not empty.
 */
public function filled(string $key): bool
{
    if (!$this->has($key)) {
        return false;
    }

    $value = $this->input($key);

    return $value !== null
        && $value !== '';
}

/**
 * Retrieve only the specified keys.
 *
 * @param array<int,string> $keys
 */
public function only(array $keys): array
{
    return array_intersect_key(
        $this->all(),
        array_flip($keys)
    );
}

/**
 * Retrieve all input except the specified keys.
 *
 * @param array<int,string> $keys
 */
public function except(array $keys): array
{
    return array_diff_key(
        $this->all(),
        array_flip($keys)
    );
}

/**
 * Get a GET parameter.
 */
public function get(
    string $key,
    mixed $default = null
): mixed {

    return $this->get[$key] ?? $default;
}

/**
 * Get a POST parameter.
 */
public function postInput(
    string $key,
    mixed $default = null
): mixed {

    return $this->post[$key] ?? $default;
}
 
 /**
 * Get uploaded files.
 */
public function files(): array
{
    return $this->files;
}

/**
 * Get cookies.
 */
public function cookies(): array
{
    return $this->cookies;
}

/**
 * Get server variables.
 */
public function server(): array
{
    return $this->server;
}

}