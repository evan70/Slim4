<?php

namespace App\Services;

class SessionManagementService
{
    private const SESSION_LIFETIME = 3600; // 1 hour in seconds
    private const ACTIVITY_THRESHOLD = 300; // 5 minutes in seconds

    public function __construct()
    {
        if (!isset($_SESSION['last_activity'])) {
            $this->initializeSession();
        }
    }

    public function initializeSession(): void
    {
        $_SESSION['last_activity'] = time();
        $_SESSION['created_at'] = time();
    }

    public function updateActivity(): void
    {
        $_SESSION['last_activity'] = time();
    }

    public function isExpired(): bool
    {
        if (!isset($_SESSION['last_activity'])) {
            return true;
        }

        $inactiveTime = time() - $_SESSION['last_activity'];
        $totalTime = time() - ($_SESSION['created_at'] ?? time());

        return $inactiveTime > self::ACTIVITY_THRESHOLD || 
               $totalTime > self::SESSION_LIFETIME;
    }

    public function destroy(): void
    {
        session_unset();
        session_destroy();
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
        $this->initializeSession();
    }

    public function getLastActivity(): int
    {
        return $_SESSION['last_activity'] ?? 0;
    }

    public function getSessionAge(): int
    {
        return time() - ($_SESSION['created_at'] ?? time());
    }
}
