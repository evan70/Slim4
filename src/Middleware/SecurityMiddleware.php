<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Models\IpWhitelist;
use App\Services\RateLimiter;
use App\Services\AuditService;
use Slim\Psr7\Factory\ResponseFactory;

class SecurityMiddleware implements MiddlewareInterface
{
    private $rateLimiter;
    private $auditService;
    
    public function __construct(RateLimiter $rateLimiter, AuditService $auditService)
    {
        $this->rateLimiter = $rateLimiter;
        $this->auditService = $auditService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'];
        $path = $request->getUri()->getPath();

        // IP Whitelisting pre admin rozhranie
        if (str_starts_with($path, '/admin')) {
            if (!$this->isIpWhitelisted($ip)) {
                $this->auditService->log('ip_whitelist_blocked', null, null, ['ip' => $ip]);
                return $this->createErrorResponse('IP nie je na whiteliste', 403);
            }
        }

        // Rate Limiting
        $rateLimitKey = $ip . ':' . $path;
        if (!$this->checkRateLimit($rateLimitKey)) {
            $this->auditService->log('rate_limit_exceeded', null, null, ['ip' => $ip, 'path' => $path]);
            return $this->createErrorResponse('Prekročený limit požiadaviek', 429);
        }

        return $handler->handle($request);
    }

    private function isIpWhitelisted(string $ip): bool
    {
        return IpWhitelist::where('ip_address', $ip)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    private function checkRateLimit(string $key): bool
    {
        $maxAttempts = (int) ($_ENV['RATE_LIMIT_MAX'] ?? 100);
        $decayMinutes = (int) ($_ENV['RATE_LIMIT_DECAY_MINUTES'] ?? 1);
        
        return $this->rateLimiter->attempt(
            $key,
            $maxAttempts,
            $decayMinutes * 60
        );
    }

    private function createErrorResponse(string $message, int $status): Response
    {
        $response = (new ResponseFactory)->createResponse($status);
        $response->getBody()->write(json_encode([
            'error' => $message,
            'status' => $status
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    }
}