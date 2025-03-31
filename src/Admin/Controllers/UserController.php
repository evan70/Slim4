<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Services\UserService;
use App\Services\TwoFactorAuthService;

class UserController
{
    private $view;
    private $userService;
    private $twoFactorAuth;

    public function __construct(
        Twig $view, 
        UserService $userService,
        TwoFactorAuthService $twoFactorAuth
    ) {
        $this->view = $view;
        $this->userService = $userService;
        $this->twoFactorAuth = $twoFactorAuth;
    }

    public function index(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();
        
        $users = User::query()
            ->when(!empty($query['search']), function ($q) use ($query) {
                return $q->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query['search']}%")
                      ->orWhere('email', 'like', "%{$query['search']}%");
                });
            })
            ->when(!empty($query['role']), function ($q) use ($query) {
                return $q->where('is_admin', $query['role'] === 'admin');
            })
            ->when(isset($query['status']), function ($q) use ($query) {
                return $q->where('is_active', $query['status'] === 'active');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->view->render($response, 'admin/users/index.twig', [
            'users' => $users,
            'query' => $query,
            'title' => 'Users Management'
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/users/create.twig', [
            'title' => 'Create User'
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $validation = $this->userService->validateUserData($data);
        if (!$validation->isValid()) {
            $this->flash->addMessage('error', $validation->getFirstError());
            return $response->withHeader('Location', '/admin/users/create')
                          ->withStatus(302);
        }

        try {
            $user = $this->userService->createUser($data);
            $this->flash->addMessage('success', 'User created successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to create user: ' . $e->getMessage());
            return $response->withHeader('Location', '/admin/users/create')
                          ->withStatus(302);
        }

        return $response->withHeader('Location', '/admin/users')
                       ->withStatus(302);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['id']);

        return $this->view->render($response, 'admin/users/edit.twig', [
            'user' => $user,
            'title' => 'Edit User'
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $user = User::findOrFail($args['id']);
        $data = $request->getParsedBody();

        $validation = $this->userService->validateUserData($data, $user->id);
        if (!$validation->isValid()) {
            $this->flash->addMessage('error', $validation->getFirstError());
            return $response->withHeader('Location', "/admin/users/edit/{$user->id}")
                          ->withStatus(302);
        }

        try {
            $this->userService->updateUser($user, $data);
            $this->flash->addMessage('success', 'User updated successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to update user: ' . $e->getMessage());
            return $response->withHeader('Location', "/admin/users/edit/{$user->id}")
                          ->withStatus(302);
        }

        return $response->withHeader('Location', '/admin/users')
                       ->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $user = User::findOrFail($args['id']);
            
            // Prevent self-deletion
            if ($user->id === $_SESSION['user_id']) {
                throw new \Exception('You cannot delete your own account');
            }

            $user->delete();
            $this->flash->addMessage('success', 'User deleted successfully');
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to delete user: ' . $e->getMessage());
        }

        return $response->withHeader('Location', '/admin/users')
                       ->withStatus(302);
    }

    public function toggleStatus(Request $request, Response $response, array $args): Response
    {
        try {
            $user = User::findOrFail($args['id']);
            
            // Prevent self-deactivation
            if ($user->id === $_SESSION['user_id']) {
                throw new \Exception('You cannot change your own status');
            }

            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'activated' : 'deactivated';
            $this->flash->addMessage('success', "User {$status} successfully");
        } catch (\Exception $e) {
            $this->flash->addMessage('error', 'Failed to update user status: ' . $e->getMessage());
        }

        return $response->withHeader('Location', '/admin/users')
                       ->withStatus(302);
    }
}