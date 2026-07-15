<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SchoolERP\Http\Request;

$request = Request::capture();

echo "Method: " . $request->method() . PHP_EOL;
echo "URI: " . $request->uri() . PHP_EOL;
echo "Path: " . $request->path() . PHP_EOL;

echo PHP_EOL . "GET:" . PHP_EOL;
print_r($request->query());

echo PHP_EOL . "POST:" . PHP_EOL;
print_r($request->post());

echo PHP_EOL . "ALL:" . PHP_EOL;
print_r($request->all());

echo PHP_EOL . "Input('name'): ";
var_dump($request->input('name'));

echo PHP_EOL . "Has('name'): ";
var_dump($request->has('name'));

echo PHP_EOL . "Filled('name'): ";
var_dump($request->filled('name'));

echo PHP_EOL . "Only(['name']):" . PHP_EOL;
print_r($request->only(['name']));

echo PHP_EOL . "Except(['name']):" . PHP_EOL;
print_r($request->except(['name']));