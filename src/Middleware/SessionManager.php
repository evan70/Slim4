<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Services\AuditService;

class SessionManager implements MiddlewareInterface
{
    private $auditService;
    
    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $this->initializeSession();
        $this->validateSession($request);
        $this->rotateSessionId();
        
        return $handler->handle($request);
    }

    private function initializeSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => true,
                'cookie_samesite' => 'Lax',
                'gc_maxlifetime' => 3600,
                'use_strict_mode' => true
            ]);
        }
    }

    private function validateSession(Request $request): void
    {
        if (isset($_SESSION['user_id'])) {
            // Kontrola IP adresy
            if (!isset($_SESSION['ip_address'])) {
                $_SESSION['ip_address'] = $request->getServerParams()['REMOTE_ADDR'];
            } elseif ($_SESSION['ip_address'] !== $request->getServerParams()['REMOTE_ADDR']) {
                $this->auditService->log('session_ip_mismatch');
                $this->destroySession();
            }

            // Kontrola User-Agent
            if (!isset($_SESSION['user_agent'])) {
                $_SESSION['user_agent'] = $request->getHeaderLine('User-Agent');
            } elseif ($_SESSION['user_agent'] !== $request->getHeaderLine('User-Agent')) {
                $this->auditService->log('session_user_agent_mismatch');
                $this->destroySession();
            }

            // Kontrola času poslednej aktivity
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
                $this->auditService->log('session_timeout');
                $this->destroySession();
            }
            $_SESSION['last_activity'] = time();
        }
    }

    private function rotateSessionId(): void
    {
        if (isset($_SESSION['user_id']) && 
            (!isset($_SESSION['last_rotation']) || 
             time() - $_SESSION['last_rotation'] > 300)) {
            session_regenerate_id(true);
            $_SESSION['last_rotation'] = time();
        }
    }

    private function destroySession(): void
    {
        session_unset();
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/');
    }
}