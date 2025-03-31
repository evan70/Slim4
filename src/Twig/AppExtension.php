<?php

namespace App\Twig;

use Slim\App;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('is_current_url', [$this, 'isCurrentUrl'])
        ];
    }

    public function isCurrentUrl(string $routeName): bool
    {
        $route = $this->app->getRouteCollector()->getNamedRoute($routeName);
        $pattern = $route->getPattern();
        
        $currentPath = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remove query string if present
        if (($pos = strpos($currentPath, '?')) !== false) {
            $currentPath = substr($currentPath, 0, $pos);
        }
        
        // For exact match
        if ($pattern === $currentPath) {
            return true;
        }
        
        // For pattern match (e.g., /documents/{filename})
        $pattern = preg_replace('/\{[^}]+\}/', '[^/]+', $pattern);
        return (bool) preg_match('#^' . $pattern . '$#', $currentPath);
    }
}
