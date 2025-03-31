<?php

// Error handling
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

set_exception_handler(function ($e) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString();
});

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;

session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

// Create Container
$container = new Container();

// Create App first
$app = AppFactory::createFromContainer($container);

// Configure Twig
$container->set(Twig::class, function () use ($app) {
    $loader = new FilesystemLoader(__DIR__ . '/../templates');
    $twig = new Twig($loader, [
        'cache' => false,
        'debug' => true,
        'auto_reload' => true
    ]);
    
    $twig->addExtension(new \App\Twig\AppExtension($app));
    
    return $twig;
});

// Configure AdminController with Twig dependency
$container->set(\App\Admin\Controllers\AdminController::class, function (Container $container) {
    return new \App\Admin\Controllers\AdminController(
        $container->get(Twig::class)
    );
});

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Add routes
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/admin.php';

$app->run();
