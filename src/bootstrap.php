<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => env('DB_CONNECTION', 'sqlite'),
    'database'  => env('DB_DATABASE', __DIR__ . '/../database/database.sqlite'),
    'host'      => env('DB_HOST', '127.0.0.1'),
    'port'      => env('DB_PORT', '3306'),
    'username'  => env('DB_USERNAME', 'root'),
    'password'  => env('DB_PASSWORD', ''),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();

// Configure the Paginator to use Bootstrap (or Tailwind if you prefer)
// Paginator::useBootstrap();
// Or for Tailwind:
Paginator::useTailwind();

// Set the current request instance for proper pagination URL generation
Paginator::currentPathResolver(function () {
    return isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
});

Paginator::currentPageResolver(function ($pageName = 'page') {
    $page = isset($_GET[$pageName]) ? $_GET[$pageName] : 1;
    return $page;
});
