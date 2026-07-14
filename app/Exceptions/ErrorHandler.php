<?php

declare(strict_types=1);

namespace SchoolERP\Exceptions;

use ErrorException;
use RuntimeException;
use Throwable;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * ErrorHandler
 * --------------------------------------------------------------------------
 *
 * Centralized framework error handling.
 *
 * Responsibilities
 * ----------------
 * • Register global PHP handlers
 * • Handle uncaught exceptions
 * • Convert PHP errors into exceptions
 * • Handle fatal shutdown errors
 * • Log framework errors
 * • Render HTTP error pages
 *
 * Design Principles
 * -----------------
 * • Zero framework dependencies
 * • Production safe
 * • Strict typing
 * • PSR-12 compliant
 * • SOLID architecture
 *
 * This class intentionally DOES NOT depend on:
 *
 * • Container
 * • Config
 * • Router
 * • Database
 * • Session
 * • Authentication
 *
 * so that it remains operational even when the framework fails
 * during bootstrap.
 */
final class ErrorHandler
{
    /**
     * Prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Location of framework error views.
     */
    private const VIEW_PATH = __DIR__ . '/../Views/errors/';

    /**
     * Directory used for framework logs.
     */
    private const LOG_DIRECTORY = __DIR__ . '/../../storage/logs/';

    /**
     * Error log filename.
     */
    private const LOG_FILE = self::LOG_DIRECTORY . 'error.log';

    /**
     * Default HTTP status.
     */
    private const DEFAULT_STATUS = 500;

    /**
     * HTTP error configuration.
     *
     * @var array<int,array{
     *     view:string,
     *     title:string,
     *     message:string
     * }>
     */
    private const ERROR_MAP = [

        401 => [
            'view' => '401',
            'title' => '401 Unauthorized',
            'message' => 'Authentication required.',
        ],

        403 => [
            'view' => '403',
            'title' => '403 Forbidden',
            'message' => 'Access denied.',
        ],

        404 => [
            'view' => '404',
            'title' => '404 Not Found',
            'message' => 'The requested page could not be found.',
        ],

        419 => [
            'view' => '419',
            'title' => '419 Session Expired',
            'message' => 'Your session has expired.',
        ],

        429 => [
            'view' => '429',
            'title' => '429 Too Many Requests',
            'message' => 'Too many requests.',
        ],

        500 => [
            'view' => '500',
            'title' => '500 Internal Server Error',
            'message' => 'An unexpected error occurred.',
        ],

        503 => [
            'view' => 'maintenance',
            'title' => '503 Service Unavailable',
            'message' => 'The application is currently under maintenance.',
        ],
    ];

    /**
     * Register all global PHP handlers.
     */
    public static function registerGlobalHandlers(): void
    {
        set_exception_handler(
            [self::class, 'handleException']
        );

        set_error_handler(
            [self::class, 'handleError']
        );

        register_shutdown_function(
            [self::class, 'handleShutdown']
        );
    }

    /**
     * Handle uncaught exceptions.
     */
    public static function handleException(
        Throwable $exception
    ): void {

        self::logException($exception);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        self::render(500);

        exit;

    }

    /**
     * Convert PHP runtime errors into exceptions.
     */
    public static function handleError(
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {

        /*
         * Respect PHP's error_reporting() level.
         */
        if (!(error_reporting() & $severity)) {
            return true;
        }

        throw new ErrorException(
            $message,
            0,
            $severity,
            $file,
            $line
        );
    }

    /**
     * Handle fatal shutdown errors.
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        $fatalTypes = [
            E_ERROR,
            E_PARSE,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_USER_ERROR,
        ];

        if (!in_array($error['type'], $fatalTypes, true)) {
            return;
        }

        self::logException(
            new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            )
        );

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        self::render(500);

        exit;

    }

    /**
     * Render an HTTP error page.
     */
    public static function render(
       int $statusCode,
       ?string $customMessage = null
    ): void {

    if (!isset(self::ERROR_MAP[$statusCode])) {
        $statusCode = self::DEFAULT_STATUS;
    }

    $config = self::ERROR_MAP[$statusCode];

    if (!headers_sent()) {
        http_response_code($statusCode);
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
     * Abort with HTTP 401 Unauthorized.
     *
     * @return never
     */
    public static function unauthorized(): never
    {
        self::abort(401);
    }

    /**
     * Abort with HTTP 403 Forbidden.
     *
     * @return never
     */
    public static function forbidden(): never
    {
        self::abort(403);
    }

    /**
     * Abort with HTTP 404 Not Found.
     *
     * @return never
     */
    public static function notFound(): never
    {
        self::abort(404);
    }

    /**
     * Abort with HTTP 419 Session Expired.
     *
     * @return never
     */
    public static function pageExpired(): never
    {
        self::abort(419);
    }

    /**
     * Abort with HTTP 429 Too Many Requests.
     *
     * @return never
     */
    public static function tooManyRequests(): never
    {
        self::abort(429);
    }

    /**
     * Abort with HTTP 500 Internal Server Error.
     *
     * @return never
     */
    public static function serverError(): never
    {
        self::abort(500);
    }

    /**
     * Abort with HTTP 503 Service Unavailable.
     *
     * @return never
     */
    public static function serviceUnavailable(): never
    {
        self::abort(503);
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

        $layoutFile = self::VIEW_PATH . 'layout.php';
        $contentFile = self::VIEW_PATH . $view . '.php';

        if (!is_file($contentFile)) {
           $contentFile = self::VIEW_PATH . '500.php';
    }

        if (!is_file($contentFile)) {
           throw new RuntimeException(
           'Missing error view: ' . $contentFile
       );
    }

        if (!is_file($layoutFile)) {
            throw new RuntimeException(
                'Missing error layout: ' . $layoutFile
            );
        }

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

        require $layoutFile;
    }

    /**
     * Log an exception.
     */
    private static function logException(
        Throwable $exception
    ): void {

    if (
        !is_dir(self::LOG_DIRECTORY)
        && !mkdir(self::LOG_DIRECTORY, 0755, true)
        &&  !is_dir(self::LOG_DIRECTORY)
    ) {
        return;
    }

        $log = sprintf(
            "[%s] %s: %s in %s on line %d%sStack Trace:%s%s%s",
            date('Y-m-d H:i:s'),
            get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        PHP_EOL,
        PHP_EOL,
        $exception->getTraceAsString(),
        PHP_EOL . PHP_EOL
       );

        file_put_contents(
            self::LOG_FILE,
            $log,
            FILE_APPEND | LOCK_EX
        );
    }
}
    