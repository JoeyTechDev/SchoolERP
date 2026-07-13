<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * SchoolERP
 * --------------------------------------------------------------------------
 * Instead of loading config/app.php directly, it should load the bootstrap
 * */

require_once __DIR__ . '/../bootstrap/app.php';

header('Location: ' . BASE_URL . '/auth/login.php');
exit;