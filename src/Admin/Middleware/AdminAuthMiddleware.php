<?php

namespace App\Admin\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AdminAuthMiddleware
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 1800; // 30 minút

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $session = $_SESSION['admin'] ?? null;
        $ip = $request->getServerParams()['REMOTE_ADDR'];
        
        // Kontrola IP blacklistu
        if ($this->isIpBlacklisted($ip)) {
            return $this->createErrorResponse('Too many login attempts. Try again later.', 429);
        }

        // Kontrola 2FA ak je nastavené
        if ($session && !$this->verifyTwoFactor($session)) {
            return $this->redirectToTwoFactor();
        }

        // Základná autentifikácia
        if (!$session && !$this->isLoginPage($request)) {
            return $this->createRedirectResponse('/dashboard/login');
        }

        // Rate limiting
        if ($this->isRateLimitExceeded($ip)) {
            return $this->createErrorResponse('Rate limit exceeded', 429);
        }

        return $handler->handle($request);
    }

    private function isLoginPage(Request $request): bool
    {
        return $request->getUri()->getPath() === '/dashboard/login';
    }

    private function isIpBlacklisted(string $ip): bool
    {
        $attempts = $_SESSION['login_attempts'][$ip] ?? 0;
        $lastAttempt = $_SESSION['last_attempt'][$ip] ?? 0;

        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            if (time() - $lastAttempt < self::LOCKOUT_TIME) {
                return true;
            }
            // Reset po uplynutí času
            unset($_SESSION['login_attempts'][$ip]);
            unset($_SESSION['last_attempt'][$ip]);
        }
        return false;
    }

    private function verifyTwoFactor(int $userId): bool
    {
        // Ak 2FA nie je povolené v konfigurácii, vrátime true
        if (!$this->isTwoFactorEnabled()) {
            return true;
        }

        // Ak je užívateľ na 2FA stránke, povolíme prístup
        if ($this->isTwoFactorPage()) {
            return true;
        }

        // Kontrola či užívateľ už prešiel 2FA
        return isset($_SESSION['2fa_verified']) && $_SESSION['2fa_verified'] === $userId;
    }

    private function isTwoFactorEnabled(): bool
    {
        return $_ENV['ADMIN_2FA_ENABLED'] ?? false;
    }

    private function isTwoFactorPage(): bool
    {
        return isset($_SERVER['REQUEST_URI']) && 
               strpos($_SERVER['REQUEST_URI'], '/dashboard/2fa') === 0;
    }

    private function redirectToTwoFactor(): Response
    {
        return $this->createRedirectResponse('/dashboard/2fa');
    }

    private function isRateLimitExceeded(string $ip): bool
    {
        $window = $_ENV['ADMIN_RATE_WINDOW'] ?? 3600; // 1 hodina default
        $limit = $_ENV['ADMIN_RATE_LIMIT'] ?? 100;    // 100 požiadaviek za okno
        
        $key = "rate_limit:{$ip}";
        $current = $_SESSION[$key] ?? 0;
        
        if ($current >= $limit) {
            return true;
        }
        
        $_SESSION[$key] = $current + 1;
        return false;
    }

    private function createRedirectResponse(string $url): Response
    {
        $response = new Response();
        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }

    private function createErrorResponse(string $message, int $status): Response
    {
        $response = new Response();
        $response->getBody()->write($message);
        return $response->withStatus($status);
    }
}
