<?php

declare(strict_types=1);

namespace SchoolERP\Http;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Response
 * --------------------------------------------------------------------------
 *
 * Represents an outgoing HTTP response.
 *
 * Responsibilities
 * ----------------
 * • Set HTTP status codes
 * • Send headers
 * • Output content
 * • Return JSON responses
 * • Handle redirects
 *
 * Design Principles
 * -----------------
 * • Zero framework dependencies
 * • Immutable-style API
 * • Strict typing
 * • PSR-12 compliant
 */
final class Response
{
    /**
     * HTTP status code.
     */
    private int $status = 200;

    /**
     * Response headers.
     *
     * @var array<string,string>
     */
    private array $headers = [];

    /**
     * Response body.
     */
    private string $content = '';

    /**
     * Private constructor.
     */
    private function __construct()
    {
    }

    /**
     * Create a new response instance.
     */
    public static function make(
        string $content = '',
        int $status = 200
    ): self {

        $response = new self();

        $response->content = $content;
        $response->status = $status;

        return $response;
    }

    /**
     * Set the HTTP status code.
     */
    public function status(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Add a response header.
     */
    public function header(
        string $name,
        string $value
    ): self {

        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Set the response body.
     */
    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }
    
/**
 * Get the response content.
 */
public function getContent(): string
{
    return $this->content;
}

/**
 * Get the response status.
 */
public function getStatus(): int
{
    return $this->status;
}

/**
 * Get response headers.
 *
 * @return array<string,string>
 */
public function getHeaders(): array
{
    return $this->headers;
}
 
/**
 * Return a JSON response.
 */
public static function json(
    array $data,
    int $status = 200
): self {

    return self::make(
        json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        ) ?: '{}',
        $status
    )->header(
        'Content-Type',
        'application/json'
    );
}

/**
 * Redirect to another URL.
 */
public static function redirect(
    string $url,
    int $status = 302
): self {

    return self::make('', $status)
        ->header(
            'Location',
            $url
        );
}

/**
 * Create a 404 response.
 */
public static function notFound(): self
{
    return self::make(
        '404 Not Found',
        404
    );
}

    /**
     * Send the response to the client.
     */
    public function send(): void
    {
        if (!headers_sent()) {

        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
    }

        echo $this->content;
    }
    
}