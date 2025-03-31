<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Services\AuditService;

class AuditMiddleware
{
    private $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $response = $handler->handle($request);

        if ($request->getMethod() !== 'GET') {
            $this->auditService->log(
                $request->getMethod(),
                $request->getUri()->getPath(),
                null,
                null,
                $request->getParsedBody()
            );
        }

        return $response;
    }
}