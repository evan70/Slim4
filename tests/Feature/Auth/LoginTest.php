<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Inicializácia session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => password_hash('password', PASSWORD_DEFAULT)
        ]);
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $request = (new ServerRequestFactory)->createServerRequest(
            'POST',
            '/dashboard/login'
        )->withParsedBody([
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response = $this->app->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/dashboard', $response->getHeader('Location')[0]);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        $request = (new ServerRequestFactory)->createServerRequest(
            'POST',
            '/dashboard/login'
        )->withParsedBody([
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);

        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Invalid credentials', (string) $response->getBody());
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        session_destroy();
        parent::tearDown();
    }
}
