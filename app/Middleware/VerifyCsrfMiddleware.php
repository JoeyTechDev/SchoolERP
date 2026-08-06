<?php

declare(strict_types=1);

namespace SchoolERP\Middleware;

use SchoolERP\Http\Request;
use SchoolERP\Http\Response;
use SchoolERP\Security\Csrf;

/**
 * --------------------------------------------------------------------------
 * SchoolERP Framework
 * --------------------------------------------------------------------------
 * Verify CSRF Middleware
 * --------------------------------------------------------------------------
 *
 * Rejects invalid CSRF tokens for unsafe HTTP methods.
 */
final class VerifyCsrfMiddleware extends Middleware
{
    /**
     * Constructor.
     */
    public function __construct(
        private Csrf $csrf
    ) {
    }

    /**
     * Handle the request.
     */
    public function handle(
        Request $request,
        callable $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Ignore safe HTTP methods
        |--------------------------------------------------------------------------
        */
        if (
            in_array(
                strtoupper($request->method()),
                ['GET', 'HEAD', 'OPTIONS'],
                true
            )
        ) {
            return $this->next(
                $request,
                $next
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate submitted token
        |--------------------------------------------------------------------------
        */
        $token = $request->post('_token');

        if (!$this->csrf->verify($token)) {
            return Response::make(
                '419 Page Expired (Invalid CSRF Token)',
                419
            );
        }

        return $this->next(
            $request,
            $next
        );
    }
}