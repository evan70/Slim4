<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use DI\Container;
use Slim\App;
use Slim\Factory\AppFactory;
use App\Admin\Controllers\AdminController;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

abstract class TestCase extends BaseTestCase
{
    protected App $app;
    protected Container $container;
    protected Capsule $db;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Inicializácia databázy
        $this->setupDatabase();
        
        // Inicializácia container
        $this->container = new Container();
        
        // Konfigurácia Twig v containeri
        $this->container->set(FilesystemLoader::class, function() {
            return new FilesystemLoader(__DIR__ . '/../templates');
        });

        $this->container->set(Twig::class, function(Container $container) {
            $loader = $container->get(FilesystemLoader::class);
            return new Twig($loader, [
                'cache' => false,
                'debug' => true,
                'auto_reload' => true
            ]);
        });
        
        // Inicializácia aplikácie
        $this->app = AppFactory::createFromContainer($this->container);
        
        // Pridanie TwigMiddleware
        $this->app->add(TwigMiddleware::createFromContainer($this->app, Twig::class));
        
        // Registrácia AdminController
        $this->container->set(AdminController::class, function ($container) {
            return new AdminController($container->get(Twig::class));
        });
        
        $this->setupRoutes();
    }

    protected function setupDatabase(): void
    {
        $capsule = new Capsule;
        
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        
        $this->db = $capsule;
        
        $this->runMigrations($capsule);
    }

    protected function runMigrations(Capsule $capsule): void
    {
        // Vytvorenie users tabuľky
        $capsule->schema()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Vytvorenie migrations tabuľky
        if (!$capsule->schema()->hasTable('migrations')) {
            $capsule->schema()->create('migrations', function ($table) {
                $table->id();
                $table->string('migration');
                $table->timestamp('executed_at');
            });
        }
    }

    protected function setupRoutes(): void
    {
        $this->app->post('/dashboard/login', [AdminController::class, 'login'])->setName('admin.login');
        $this->app->get('/dashboard/login', [AdminController::class, 'login'])->setName('admin.login');
        $this->app->get('/dashboard', [AdminController::class, 'dashboard'])->setName('admin.dashboard');
        $this->app->get('/dashboard/logout', [AdminController::class, 'logout'])->setName('admin.logout');
    }

    protected function tearDown(): void
    {
        Capsule::schema()->dropIfExists('users');
        Capsule::schema()->dropIfExists('migrations');
        parent::tearDown();
    }
}
