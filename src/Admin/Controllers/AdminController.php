<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use Carbon\Carbon;
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
            
            error_log('=== Login Attempt Debug ===');
            error_log('Attempting login for email: ' . ($data['email'] ?? 'not set'));
            
            $user = User::where('email', $data['email'])->first();
            
            if ($user) {
                error_log('User found with ID: ' . $user->id);
                error_log('Stored password hash: ' . $user->password);
                error_log('Input password (first 5 chars): ' . substr($data['password'], 0, 5));
                
                $verified = password_verify($data['password'], $user->password);
                error_log('Password verification result: ' . ($verified ? 'SUCCESS' : 'FAILED'));
                
                if ($verified) {
                    error_log('Login successful - setting session');
                    $_SESSION['admin'] = $user->id;
                    return $response
                        ->withHeader('Location', '/dashboard')
                        ->withStatus(302);
                }
            } else {
                error_log('No user found with email: ' . $data['email']);
            }

            return $this->view->render($response, 'admin/login.twig', [
                'error' => 'Invalid credentials',
                'email' => $data['email']
            ]);
        }

        return $this->view->render($response, 'admin/login.twig');
    }

    public function dashboard(Request $request, Response $response): Response
    {
        $stats = [
            'users' => User::count(),
            'posts' => 0, // Ak máte Post model: Post::count()
            'comments' => 0, // Ak máte Comment model: Comment::count()
            'active_users' => User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count()
        ];

        $activities = []; // Tu môžete pridať aktivity ak máte Activity/AuditLog model
        // Príklad:
        // $activities = AuditLog::with('user')
        //     ->orderBy('created_at', 'desc')
        //     ->limit(10)
        //     ->get();

        return $this->view->render($response, 'admin/dashboard.twig', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'activities' => $activities
        ]);
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

    public function profile(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/profile.twig', [
            'title' => 'My Profile'
        ]);
    }

    public function getRecentActivity(Request $request, Response $response): Response
    {
        // Example activity data - replace with your actual data retrieval logic
        $activities = [
            [
                'id' => 1,
                'icon' => 'fas fa-user',
                'iconClass' => 'bg-blue-100 text-blue-500',
                'description' => 'New user registered',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'user' => [
                    'name' => 'John Doe',
                    'avatar' => 'https://ui-avatars.com/api/?name=John+Doe'
                ]
            ],
            [
                'id' => 2,
                'icon' => 'fas fa-file',
                'iconClass' => 'bg-green-100 text-green-500',
                'description' => 'New post created',
                'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'user' => [
                    'name' => 'Jane Smith',
                    'avatar' => 'https://ui-avatars.com/api/?name=Jane+Smith'
                ]
            ]
        ];

        $response->getBody()->write(json_encode([
            'activities' => $activities
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function help(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/help.twig', [
            'title' => 'Help & Documentation'
        ]);
    }
}
