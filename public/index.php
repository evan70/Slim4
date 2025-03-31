<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;
use App\Admin\Middleware\AdminAuthMiddleware;
use App\Services\TwoFactorAuthService;
use App\Services\SessionManagementService;

session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

// Create Container
$container = new Container();

// Configure services
$container->set(TwoFactorAuthService::class, function (Container $container) {
    return new TwoFactorAuthService();
});

$container->set(SessionManagementService::class, function (Container $container) {
    return new SessionManagementService();
});

// Configure AdminAuthMiddleware
$container->set('admin_auth_middleware', function (Container $container) {
    return new AdminAuthMiddleware(
        $container->get(TwoFactorAuthService::class),
        $container->get(SessionManagementService::class)
    );
});

// Create App
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
