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
            new TwigFunction('is_current_path', [$this, 'isCurrentPath'])
        ];
    }

    public function isCurrentPath(string $routeName): bool
    {
        $route = $this->app->getRouteCollector()->getNamedRoute($routeName);
        $routePath = $route->getPattern();
        
        $currentPath = $_SERVER['REQUEST_URI'];
        // Remove query string if any
        if (($pos = strpos($currentPath, '?')) !== false) {
            $currentPath = substr($currentPath, 0, $pos);
        }
        
        return $currentPath === $routePath;
    }
}