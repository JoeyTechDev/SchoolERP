<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * SchoolERP Error Handler
 * --------------------------------------------------------------------------
 *
 * Centralized error handling and rendering system.
 *
 * Responsibilities:
 * - Render HTTP error pages
 * - Register global exception handlers
 * - Register PHP runtime error handlers
 * - Register fatal shutdown handlers
 * - Prevent sensitive error leakage
 *
 * PHP Version: 8.2+
 * --------------------------------------------------------------------------
 */

final class ErrorHandler
{
    /**
     * Location of all error views.
     */
    private const VIEWS_PATH = __DIR__ . '/../Views/errors/';

    /**
     * Default HTTP status.
     */
    private const DEFAULT_STATUS = 500;

    /**
     * HTTP Error map.
     *
     * @var array<int,array{
     *      view:string,
     *      title:string,
     *      message:string
     * }>
     */
    private const ERROR_MAP = [

        401 => [
            'view' => '401',
            'title' => '401 - Unauthorized',
            'message' => 'You must be logged in to access this page.',
        ],

        403 => [
            'view' => '403',
            'title' => '403 - Access Denied',
            'message' => 'You do not have permission to access this page.',
        ],

        404 => [
            'view' => '404',
            'title' => '404 - Page Not Found',
            'message' => 'The page you are looking for could not be found.',
        ],

        419 => [
            'view' => '419',
            'title' => '419 - Session Expired',
            'message' => 'Your session has expired. Please login again.',
        ],

        429 => [
            'view' => '429',
            'title' => '429 - Too Many Requests',
            'message' => 'Too many requests. Please try again later.',
        ],

        500 => [
            'view' => '500',
            'title' => '500 - Internal Server Error',
            'message' => 'Something went wrong. Please try again later.',
        ],

        503 => [
            'view' => 'maintenance',
            'title' => '503 - Maintenance',
            'message' => 'The system is currently under maintenance.',
        ],
    ];

    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Render an error page.
     */
    public static function render(
        int $statusCode,
        ?string $customMessage = null
    ): void {

        $requestedStatus = $statusCode;

        if (!array_key_exists($statusCode, self::ERROR_MAP)) {
            $statusCode = self::DEFAULT_STATUS;
        }

        $config = self::resolveConfig($statusCode);

        if (!headers_sent()) {
            http_response_code($statusCode);
        } else {
            error_log(
                sprintf(
                    'Headers already sent while rendering HTTP %d.',
                    $requestedStatus
                )
            );
        }

        self::renderView(
            view: $config['view'],
            title: $config['title'],
            message: $customMessage ?? $config['message'],
            statusCode: $statusCode
        );
    }

    /**
     * Render an error page and terminate execution.
     *
     * @return never
     */
    public static function abort(
        int $statusCode,
        ?string $customMessage = null
    ): never {

        self::render(
            $statusCode,
            $customMessage
        );

        exit;
    }

    /**
     * 401 Unauthorized
     *
     * @return never
     */
    public static function unauthorized(): never
    {
        self::abort(401);
    }

    /**
     * 403 Forbidden
     *
     * @return never
     */
    public static function forbidden(): never
    {
        self::abort(403);
    }

    /**
     * 404 Not Found
     *
     * @return never
     */
    public static function notFound(): never
    {
        self::abort(404);
    }

    /**
     * 419 Session Expired
     *
     * @return never
     */
    public static function pageExpired(): never
    {
        self::abort(419);
    }

    /**
     * 429 Too Many Requests
     *
     * @return never
     */
    public static function tooManyRequests(): never
    {
        self::abort(429);
    }

    /**
     * 500 Internal Server Error
     *
     * @return never
     */
    public static function serverError(): never
    {
        self::abort(500);
    }

    /**
     * 503 Maintenance Mode
     *
     * @return never
     */
    public static function serviceUnavailable(): never
    {
        self::abort(503);
    }

    /**
     * Register global handlers.
     */
    public static function registerGlobalHandlers(): void
    {
        set_exception_handler(
            [self::class, 'handleUncaughtException']
        );

        set_error_handler(
            [self::class, 'handleRuntimeError']
        );

        register_shutdown_function(
            [self::class, 'handleFatalShutdown']
        );
    }

        /**
     * Handle uncaught exceptions.
     */
    public static function handleUncaughtException(
        Throwable $exception
    ): void {

        error_log(sprintf(
            "[%s] Uncaught Exception: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        self::render(500);
    }

    /**
     * Handle PHP runtime errors.
     */
    public static function handleRuntimeError(
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {

        if (!(error_reporting() & $severity)) {
            return true;
        }

        error_log(sprintf(
            "[%s] PHP Error [%d]: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $severity,
            $message,
            $file,
            $line
        ));

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        self::render(500);

        return true;
    }

    /**
     * Handle fatal shutdown errors.
     */
    public static function handleFatalShutdown(): void
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        $fatalErrors = [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
        ];

        if (!in_array($error['type'], $fatalErrors, true)) {
            return;
        }

        error_log(sprintf(
            "[%s] Fatal Error: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        ));

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            http_response_code(500);
        }

        self::render(500);
    }

    /**
     * Resolve an HTTP status configuration.
     *
     * @return array{
     *      view:string,
     *      title:string,
     *      message:string
     * }
     */
    private static function resolveConfig(
        int $statusCode
    ): array {

        return self::ERROR_MAP[$statusCode]
            ?? self::ERROR_MAP[self::DEFAULT_STATUS];
    }

    /**
     * Render an error view.
     */
    private static function renderView(
        string $view,
        string $title,
        string $message,
        int $statusCode
    ): void {

        $contentFile = self::VIEWS_PATH . $view . '.php';

        if (!is_file($contentFile)) {

            error_log(sprintf(
                "[%s] Missing error view: %s",
                date('Y-m-d H:i:s'),
                $contentFile
            ));

            $contentFile = self::VIEWS_PATH . '500.php';
        }

        $layoutFile = self::VIEWS_PATH . 'layout.php';

        if (!is_file($layoutFile)) {

            throw new RuntimeException(
                sprintf(
                    'Missing error layout: %s',
                    $layoutFile
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Escape Output
        |--------------------------------------------------------------------------
        |
        | Prevent XSS by escaping every dynamic variable before it reaches
        | the view.
        |
        */

        $title = htmlspecialchars(
            $title,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $message = htmlspecialchars(
            $message,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $statusCode = (int) $statusCode;

        require $layoutFile;
    }
}