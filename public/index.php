<?php

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

session_start();

require __DIR__ . '/../vendor/autoload.php';

// Create Container
$container = new Container();

// Set view in container
$container->set('view', function() {
    return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
});

// Create App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));

// Add Error Middleware
$app->addErrorMiddleware(true, true, true);

// Add routes
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/admin.php';

$app->run();
