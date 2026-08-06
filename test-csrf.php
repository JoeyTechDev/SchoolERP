<?php

require_once __DIR__ . '/bootstrap/app.php';

echo "CSRF TEST\n";
echo "=========\n\n";

echo "Token:\n";
echo csrf_token() . PHP_EOL . PHP_EOL;

echo "Field:\n";
echo csrf_field() . PHP_EOL;