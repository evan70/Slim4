<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AdminController
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function dashboard(Request $request, Response $response)
    {
        return $this->view->render($response, 'admin/dashboard.twig', [
            'title' => 'Dashboard'
        ]);
    }

    public function login(Request $request, Response $response)
    {
        return $this->view->render($response, 'admin/login.twig', [
            'title' => 'Login'
        ]);
    }
}