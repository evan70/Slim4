<?php

namespace App\Services;

use Redis;
use RuntimeException;

class RateLimiter
{
    private $redis;
    private $prefix = 'rate_limit:';

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(
            $_ENV['REDIS_HOST'] ?? 'localhost',
            $_ENV['REDIS_PORT'] ?? 6379
        );
    }

    public function attempt(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $key = $this->prefix . $key;
        
        $current = $this->redis->get($key);
        
        if ($current === false) {
            $this->redis->setex($key, $decaySeconds, 1);
            return true;
        }
        
        if ($current >= $maxAttempts) {
            return false;
        }
        
        $this->redis->incr($key);
        return true;
    }

    public function getRemainingAttempts(string $key, int $maxAttempts): int
    {
        $key = $this->prefix . $key;
        $current = (int) $this->redis->get($key);
        return max(0, $maxAttempts - $current);
    }

    public function clear(string $key): void
    {
        $this->redis->del($this->prefix . $key);
    }
}