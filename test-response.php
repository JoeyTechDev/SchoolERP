<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Http\Response;

Response::make()
    ->status(200)
    ->header('Content-Type', 'text/plain')
    ->content("Hello from SchoolERP Response!")
    ->send();