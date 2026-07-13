<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * SchoolERP Bootstrap
 * --------------------------------------------------------------------------
 *
 * Initializes the SchoolERP application.
 *
 * Responsibilities:
 * - Load application configuration
 * - Load the Error Handler
 * - Load the Session Helper
 * - Register global error handlers
 * - Start a secure session
 *
 * Future responsibilities:
 * - Composer Autoloader
 * - Environment Variables
 * - CSRF Protection
 * - Database Connection
 * - Dependency Container
 * --------------------------------------------------------------------------
 */

// --------------------------------------------------------------------------
// Load application configuration.
// --------------------------------------------------------------------------
require_once __DIR__ . '/../config/app.php';

// --------------------------------------------------------------------------
// Load Error Handler.
// --------------------------------------------------------------------------
require_once __DIR__ . '/../app/Exceptions/ErrorHandler.php';

// --------------------------------------------------------------------------
// Load Session Helper.
// --------------------------------------------------------------------------
require_once __DIR__ . '/../app/Helpers/Session.php';

// --------------------------------------------------------------------------
// Register global exception/error handlers.
// --------------------------------------------------------------------------
ErrorHandler::registerGlobalHandlers();

// --------------------------------------------------------------------------
// Start a secure session.
// --------------------------------------------------------------------------
startSecureSession();