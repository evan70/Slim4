<?php

namespace App\Admin\Middleware;

use DI\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

class AdminAuthMiddlewareFactory
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $middleware = new AdminAuthMiddleware(
            $this->container->get(TwoFactorAuthService::class),
            $this->container->get(SessionManagementService::class)
        );

        return $middleware($request, $handler);
    }
}
