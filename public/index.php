<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;
use App\Admin\Middleware\AdminAuthMiddleware;
use App\Admin\Middleware\AdminAuthMiddlewareFactory;
use App\Services\TwoFactorAuthService;
use App\Services\SessionManagementService;
use App\Services\MarkdownService;
use App\Controllers\HomeController;
use App\Controllers\DocumentController;
use App\Admin\Controllers\DocumentController as AdminDocumentController;

session_start();

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

// Create Container
$container = new Container();

// Create App
$app = AppFactory::createFromContainer($container);

// Configure Twig and its dependencies
$container->set(FilesystemLoader::class, function () {
    $loader = new FilesystemLoader();
    $loader->addPath(__DIR__ . '/../templates');
    return $loader;
});

$container->set(Twig::class, function (Container $container) {
    return new Twig(
        $container->get(FilesystemLoader::class),
        [
            'cache' => false,
            'debug' => true,
            'auto_reload' => true
        ]
    );
});

// Configure services
$container->set(TwoFactorAuthService::class, function (Container $container) {
    return new TwoFactorAuthService();
});

$container->set(SessionManagementService::class, function (Container $container) {
    return new SessionManagementService();
});

$container->set(MarkdownService::class, function (Container $container) {
    return new MarkdownService(__DIR__ . '/../docs');
});

// Configure Admin Auth Middleware
$container->set('admin_auth_middleware', function (Container $container) {
    return new AdminAuthMiddlewareFactory($container);
});

// Configure Controllers
$container->set(HomeController::class, function (Container $container) {
    return new HomeController(
        $container->get(Twig::class),
        $container->get(MarkdownService::class)
    );
});

$container->set(DocumentController::class, function (Container $container) {
    return new DocumentController(
        $container->get(Twig::class),
        $container->get(MarkdownService::class)
    );
});

$container->set(AdminDocumentController::class, function (Container $container) {
    return new AdminDocumentController(
        $container->get(Twig::class),
        $container->get(MarkdownService::class)
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
