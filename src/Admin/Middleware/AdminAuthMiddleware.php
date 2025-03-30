<?php

namespace App\Admin\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AdminAuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $session = $_SESSION['admin'] ?? null;
        
        if (!$session && !$this->isLoginPage($request)) {
            $response = new Response();
            return $response
                ->withHeader('Location', '/admin/login')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }

    private function isLoginPage(Request $request): bool
    {
        return $request->getUri()->getPath() === '/admin/login';
    }
}