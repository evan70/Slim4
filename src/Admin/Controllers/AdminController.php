<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use App\Models\Setting;
use Slim\Views\Twig;

class AdminController
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function login(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $user = User::where('email', $data['email'])->first();

            if ($user && password_verify($data['password'], $user->password)) {
                $_SESSION['admin'] = $user->id;
                return $response
                    ->withHeader('Location', '/dashboard')
                    ->withStatus(302);
            }

            return $this->view->render($response, 'admin/login.twig', [
                'error' => 'Invalid credentials'
            ]);
        }

        return $this->view->render($response, 'admin/login.twig');
    }

    public function dashboard(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/dashboard.twig');
    }

    public function logout(Request $request, Response $response): Response
    {
        unset($_SESSION['admin']);
        return $response
            ->withHeader('Location', '/dashboard/login')
            ->withStatus(302);
    }

    public function users(Request $request, Response $response): Response
    {
        $users = User::paginate(10);
        
        return $this->view->render($response, 'admin/users/index.twig', [
            'users' => $users
        ]);
    }

    public function createUser(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/users/create.twig');
    }

    public function storeUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validácia
        if (empty($data['email']) || empty($data['password']) || empty($data['name'])) {
            return $this->view->render($response, 'admin/users/create.twig', [
                'error' => 'All fields are required',
                'data' => $data
            ]);
        }

        // Kontrola existujúceho emailu
        if (User::where('email', $data['email'])->exists()) {
            return $this->view->render($response, 'admin/users/create.twig', [
                'error' => 'Email already exists',
                'data' => $data
            ]);
        }

        // Vytvorenie používateľa
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'is_admin' => $data['is_admin'] ?? false,
            'email_verified_at' => $data['verified'] ?? null
        ]);

        // Presmerovanie späť na zoznam používateľov
        return $response
            ->withHeader('Location', '/dashboard/users')
            ->withStatus(302);
    }

    public function editUser(Request $request, Response $response, array $args): Response
    {
        $user = User::find($args['id']);
        
        if (!$user) {
            // Možno pridať flash message
            return $response
                ->withHeader('Location', '/dashboard/users')
                ->withStatus(302);
        }

        return $this->view->render($response, 'admin/users/edit.twig', [
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $user = User::find($args['id']);
        $data = $request->getParsedBody();

        if (!$user) {
            return $response
                ->withHeader('Location', '/dashboard/users')
                ->withStatus(302);
        }

        // Validácia
        if (empty($data['email']) || empty($data['name'])) {
            return $this->view->render($response, 'admin/users/edit.twig', [
                'error' => 'Name and email are required',
                'user' => $user
            ]);
        }

        // Kontrola emailu
        $existingUser = User::where('email', $data['email'])
            ->where('id', '!=', $user->id)
            ->first();
            
        if ($existingUser) {
            return $this->view->render($response, 'admin/users/edit.twig', [
                'error' => 'Email already exists',
                'user' => $user
            ]);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        
        if (!empty($data['password'])) {
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $user->save();

        return $response
            ->withHeader('Location', '/dashboard/users')
            ->withStatus(302);
    }

    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $user = User::find($args['id']);
        
        if ($user) {
            $user->delete();
        }

        return $response
            ->withHeader('Location', '/dashboard/users')
            ->withStatus(302);
    }

    public function settings(Request $request, Response $response): Response
    {
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            
            // Uloženie nastavení
            foreach ($data as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            return $response
                ->withHeader('Location', '/dashboard/settings')
                ->withStatus(302);
        }

        // Načítanie existujúcich nastavení
        $settings = Setting::pluck('value', 'key')->toArray();

        return $this->view->render($response, 'admin/settings.twig', [
            'settings' => $settings
        ]);
    }
}
