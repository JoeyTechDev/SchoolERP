<?php

declare(strict_types=1);

/**
 * ------------------------------------------------------------------------
 * Authenticate.php
 * ------------------------------------------------------------------------
 * SchoolERP
 *
 * Authentication middleware.
 *
 * Ensures that only authenticated users can access protected pages.
 * ------------------------------------------------------------------------
 */

require_once __DIR__ . '/../Helpers/Session.php';

startSecureSession();

/**
 * Ensure the current user is authenticated.
 */
function requireAuthentication(): void
{
// ----------------------------------------------------------
// Validate browser fingerprint.
// If the browser changes unexpectedly,
// terminate the session immediately.
// ----------------------------------------------------------
if (
    isset($_SESSION['user_agent']) &&
    ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? ''))
) {

    logoutUser();

    startSecureSession();

    $_SESSION['login_error'] =
        'Your session has expired. Please log in again.';

    header('Location: /SchoolERP/public/auth/login.php');
    exit;
}
    // --------------------------------------------------------------------
    // Check whether the user is logged in.
    // --------------------------------------------------------------------
    if (!isLoggedIn()) {

        logoutUser();

        startSecureSession();

        $_SESSION['login_error'] = 'Please log in to continue.';

        header('Location: /SchoolERP/public/auth/login.php');
        exit;
    }

    // --------------------------------------------------------------------
    // Verify the browser User-Agent.
    // --------------------------------------------------------------------
    if (
        ($_SESSION['user_agent'] ?? '') !== ($_SERVER['HTTP_USER_AGENT'] ?? '')
    ) {

        logoutUser();

        startSecureSession();

        $_SESSION['login_error'] = 'Your session has expired. Please log in again.';

        header('Location: /SchoolERP/public/auth/login.php');
        exit;
    }
}